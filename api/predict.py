from flask import Flask, request, jsonify
import joblib
import re
import os

app = Flask(__name__)

# Base path for models
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
model_path = os.path.join(BASE_DIR, "model.pkl")
vectorizer_path = os.path.join(BASE_DIR, "vectorizer.pkl")

# Load models
model = None
vectorizer = None
if os.path.exists(model_path) and os.path.exists(vectorizer_path):
    try:
        model = joblib.load(model_path)
        vectorizer = joblib.load(vectorizer_path)
    except Exception:
        pass

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
        return jsonify({"status": "error", "message": "ML Model failed to load on Vercel"}), 200
    
    try:
        data = request.json
        job_text = data.get("text", "")
        if not job_text:
            return jsonify({"status": "error", "message": "No text provided"}), 200

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
        return jsonify({"status": "error", "message": str(e)}), 200

# This is the Vercel requirement for a serverless function exported as app
def handler(event, context):
    return app(event, context)
