from flask import Flask, request, jsonify
import joblib
import re
import pytesseract
from PIL import Image
import os

app = Flask(__name__)

# Optional: Set tesseract path if it's not in your PATH
# Set tesseract path
# 1. Use environment variable if set
# 2. Use common Linux path (Render, Ubuntu, etc.)
# 3. Fallback to common Windows path
tesseract_path = os.getenv('TESSERACT_PATH')
if not tesseract_path:
    if os.name == 'posix':
        tesseract_path = '/usr/bin/tesseract'
    else:
        tesseract_path = r'C:\Program Files\Tesseract-OCR\tesseract.exe'

if os.path.exists(tesseract_path) or os.name == 'posix':
    pytesseract.pytesseract.tesseract_cmd = tesseract_path

# Base path for models
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
model_path = os.path.join(BASE_DIR, "model.pkl")
vectorizer_path = os.path.join(BASE_DIR, "vectorizer.pkl")

# Global variables for models (loaded lazily)
model = None
vectorizer = None

def get_model():
    global model, vectorizer
    if model is None:
        try:
            print("Loading models lazily...")
            model = joblib.load(model_path)
            vectorizer = joblib.load(vectorizer_path)
        except Exception as e:
            print(f"CRITICAL: Failed to load models: {str(e)}")
    return model, vectorizer

@app.route("/", methods=["GET"])
def home():
    m, v = get_model()
    return jsonify({
        "status": "online",
        "message": "ScamShield AI API is running",
        "model_loaded": m is not None,
        "debug_timestamp": "2026-03-18 23:48",
        "endpoints": ["/predict", "/predict-image"]
    })

def clean_text(text):
    text = re.sub(r'\W', ' ', text)
    return text.lower()

def get_reason(text, prediction):
    text = text.lower()
    if prediction == "Fake":
        if any(word in text for word in ["payment", "fee", "money", "bank", "account", "transfer"]):
            return "Contains references to personal financial transactions or upfront payments."
        if any(word in text for word in ["urgently", "limited", "immediate", "whatsapp", "telegram"]):
            return "Uses high-pressure tactics or unprofessional communication channels."
        if any(word in text for word in ["no experience", "easy", "high pay", "work from home"]):
            return "Offers unusually high rewards for vague or minimal job requirements."
        return "The job structure and language match known fraudulent recruitment patterns."
    else:
        if any(word in text for word in ["interview", "assessment", "procedure", "application"]):
            return "Follows a structured recruitment process including professional evaluations."
        if any(word in text for word in ["responsibilities", "requirements", "skills", "experience"]):
            return "Clearly defines technical skills and specific professional responsibilities."
        return "Exhibits professional corporate communication and standard industry terminology."

@app.route("/predict", methods=["POST"])
def predict():
    m, v = get_model()
    if not m or not v:
        return jsonify({"status": "error", "message": "ML Model not available. Try again in 30 seconds."}), 200
    try:
        data = request.get_json(silent=True) or {}
        job_text = data.get("text", "")
        if not job_text:
            return jsonify({"status": "error", "message": "No text provided"}), 200

        clean = clean_text(job_text)
        vec = v.transform([clean])
        prediction = m.predict(vec)[0]
        confidence = m.predict_proba(vec).max() * 100
        return jsonify({
            "status": "success",
            "result": prediction,
            "confidence": round(confidence, 2),
            "reason": get_reason(job_text, prediction)
        })
    except Exception as e:
        return jsonify({"status": "error", "message": f"Server Error: {str(e)}"}), 200

@app.route("/predict-image", methods=["POST"])
def predict_image():
    m, v = get_model()
    if not m or not v:
        return jsonify({"status": "error", "message": "ML Model not available. Try again in 30 seconds."}), 200
    if 'image' not in request.files:
        return jsonify({"status": "error", "message": "No image uploaded"}), 200
    
    file = request.files['image']
    if file.filename == '':
        return jsonify({"status": "error", "message": "No image selected"}), 200

    try:
        # Check image size to prevent memory crash (Max 5MB)
        file.seek(0, os.SEEK_END)
        size = file.tell()
        file.seek(0)
        if size > 5 * 1024 * 1024:
            return jsonify({"status": "error", "message": "Image too large (Max 5MB). Please use Text Analysis."}), 200

        img = Image.open(file)
        extracted_text = pytesseract.image_to_string(img)
        if not extracted_text.strip():
            return jsonify({"status": "error", "message": "No text found in image. Please use Text Analysis."}), 200

        clean = clean_text(extracted_text)
        vec = v.transform([clean])
        prediction = m.predict(vec)[0]
        confidence = m.predict_proba(vec).max() * 100
        return jsonify({
            "status": "success",
            "result": prediction,
            "confidence": round(confidence, 2),
            "reason": get_reason(extracted_text, prediction),
            "extracted_text": extracted_text.strip()
        })
    except Exception as e:
        return jsonify({"status": "error", "message": f"OCR Error: {str(e)}"}), 200

if __name__ == "__main__":
    app.run(debug=True)
