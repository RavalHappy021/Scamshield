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

# Load trained model and vectorizer with error handling
try:
    model = joblib.load(model_path)
    vectorizer = joblib.load(vectorizer_path)
except Exception as e:
    print(f"CRITICAL: Failed to load models: {str(e)}")
    model = None
    vectorizer = None

@app.route("/", methods=["GET"])
def home():
    import sklearn
    return jsonify({
        "status": "online",
        "message": "ScamShield AI API is running",
        "sklearn_version": sklearn.__version__,
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
    if not model or not vectorizer:
        return jsonify({"status": "error", "message": "ML Model not loaded on server. Please check logs."}), 500
    try:
        data = request.json
        job_text = data.get("text", "")
        
        if not job_text:
            return jsonify({"status": "error", "message": "No text provided"}), 400

        clean = clean_text(job_text)
        vec = vectorizer.transform([clean])

        prediction = model.predict(vec)[0]
        confidence = model.predict_proba(vec).max() * 100
        reason = get_reason(job_text, prediction)

        return jsonify({
            "status": "success",
            "result": prediction,
            "confidence": round(confidence, 2),
            "reason": reason
        })
    except Exception as e:
        print(f"Prediction Error: {str(e)}")
        return jsonify({"status": "error", "message": f"Server Error: {str(e)}"}), 500

@app.route("/predict-image", methods=["POST"])
def predict_image():
    if not model or not vectorizer:
        return jsonify({"status": "error", "message": "ML Model not loaded on server. Please check logs."}), 500
    if 'image' not in request.files:
        return jsonify({"status": "error", "message": "No image uploaded"}), 400
    
    file = request.files['image']
    if file.filename == '':
        return jsonify({"status": "error", "message": "No image selected"}), 400

    try:
        # Load image and perform OCR
        img = Image.open(file)
        extracted_text = pytesseract.image_to_string(img)
        
        if not extracted_text.strip():
            return jsonify({
                "status": "error", 
                "message": "Could not extract any text from the image. Please ensure it's a clear job poster."
            }), 200

        # Existing prediction logic
        clean = clean_text(extracted_text)
        vec = vectorizer.transform([clean])

        prediction = model.predict(vec)[0]
        confidence = model.predict_proba(vec).max() * 100
        reason = get_reason(extracted_text, prediction)

        return jsonify({
            "status": "success",
            "extracted_text": extracted_text.strip(),
            "result": prediction,
            "confidence": round(confidence, 2),
            "reason": reason
        })

    except pytesseract.TesseractNotFoundError:
        print("Error: Tesseract OCR not found.")
        return jsonify({
            "status": "error", 
            "message": f"Tesseract OCR not found. Please ensure Tesseract is installed and the path '{tesseract_path}' is correct."
        }), 200
    except Exception as e:
        print(f"Prediction Error: {str(e)}")
        return jsonify({"status": "error", "message": f"Server Error: {str(e)}"}), 500

if __name__ == "__main__":
    app.run(debug=True)
