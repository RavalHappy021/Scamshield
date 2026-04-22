import os
import joblib
import pytesseract
from PIL import Image
from flask import Flask, request, jsonify
from flask_cors import CORS
import traceback

app = Flask(__name__)
CORS(app)

# 🧠 LOAD MODELS
try:
    model = joblib.load("model.pkl")
    vectorizer = joblib.load("vectorizer.pkl")
    print("✅ Models loaded successfully.")
except Exception as e:
    model = None
    vectorizer = None
    print(f"⚠️ Note: Running in Rule-only mode. Error: {e}")

# 🛑 CROSS-PLATFORM TESSERACT SETTINGS
if os.name == 'nt': # Windows (Localhost)
    tesseract_path = r'C:\Program Files\Tesseract-OCR\tesseract.exe'
    if os.path.exists(tesseract_path):
        pytesseract.pytesseract.tesseract_cmd = tesseract_path
        print("✅ Windows Tesseract Set.")
else: # Linux (Render)
    pytesseract.pytesseract.tesseract_cmd = '/usr/bin/tesseract'
    print("✅ Linux Tesseract Set.")

SCAM_KEYWORDS = ["registration fee", "processing fee", "whatsapp", "telegram", "earn weekly", "urgent hiring", "security deposit"]

def analyze_logic(text):
    text = text.lower()
    score = 0
    reasons = []
    found_keywords = [word for word in SCAM_KEYWORDS if word in text]
    if len(found_keywords) > 0:
        score += 30
        reasons.append(f"Suspicious words found")
    
    if score >= 30: return "Fake", 85, ". ".join(reasons)
    return "Real", 70, "Clean"

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()
        if not data or 'text' not in data:
            return jsonify({"status": "error", "message": "No text provided"}), 400
        
        res, conf, reason = analyze_logic(data['text'])
        return jsonify({
            "status": "success",
            "result": res,
            "confidence": conf,
            "reason": reason
        })
    except Exception as e:
        print(f"🔥 TEXT API ERROR: {e}")
        return jsonify({"status": "error", "message": str(e)}), 500

@app.route('/predict-image', methods=['POST'])
def predict_image():
    try:
        if 'image' not in request.files:
            return jsonify({"status": "error", "message": "No image uploaded"}), 400
        
        file = request.files['image']
        img = Image.open(file.stream)
        
        # Try OCR
        try:
            text = pytesseract.image_to_string(img)
        except Exception as ocr_err:
            print(f"❌ OCR FAILED: {ocr_err}")
            return jsonify({"status": "error", "message": "Tesseract not installed on your PC. Please install it or use Text Analysis."}), 500

        res, conf, reason = analyze_logic(text)
        return jsonify({
            "status": "success",
            "result": res,
            "confidence": conf,
            "reason": reason,
            "extracted_text": text[:300]
        })
    except Exception as e:
        print("🔥 SERVER CRASH:")
        traceback.print_exc()
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
