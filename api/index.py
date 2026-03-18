from flask import Flask, request, jsonify
import joblib
import re
import os
from PIL import Image
import pytesseract

app = Flask(__name__)

# Base path for models (Vercel serverless environment)
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
model_path = os.path.join(BASE_DIR, "model.pkl")
vectorizer_path = os.path.join(BASE_DIR, "vectorizer.pkl")

# Load trained model and vectorizer
try:
    model = joblib.load(model_path)
    vectorizer = joblib.load(vectorizer_path)
except Exception as e:
    print(f"Error loading models: {str(e)}")
    # Fallback/Error handle for Vercel
    model = None
    vectorizer = None

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

@app.route("/api/predict", methods=["POST"])
def predict():
    if not model or not vectorizer:
        return jsonify({"status": "error", "message": "Model not loaded on server"}), 500
    
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
        return jsonify({"status": "error", "message": str(e)}), 500

@app.route("/api/predict-image", methods=["POST"])
def predict_image():
    if 'image' not in request.files:
        return jsonify({"status": "error", "message": "No image uploaded"}), 400
    
    file = request.files['image']
    try:
        img = Image.open(file)
        # Check if tesseract is available
        try:
            extracted_text = pytesseract.image_to_string(img)
        except Exception:
            return jsonify({
                "status": "error", 
                "message": "OCR service (Tesseract) is not available in this environment. Please use text analysis."
            }), 200

        if not extracted_text.strip():
            return jsonify({"status": "error", "message": "Could not extract text from image"}), 200

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
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

@app.route("/", defaults={"path": ""})
@app.route("/<path:path>")
def catch_all(path):
    return jsonify({"status": "online", "message": "ScamShield API is running"}), 200

# For Vercel, we don't need app.run()
