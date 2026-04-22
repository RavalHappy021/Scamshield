import os
import joblib
import pytesseract
from PIL import Image
from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

if os.name == 'nt':
    pytesseract.pytesseract.tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'

try:
    model = joblib.load("model.pkl")
    vectorizer = joblib.load("vectorizer.pkl")
    print("LOG: Models loaded.")
except:
    model = None; vectorizer = None
    print("LOG: Rule-mode only.")

SCAM_KEYWORDS = ["pay", "fee", "registration", "deposit", "money", "invest", "join", "channel", "telegram", "whatsapp", "earn daily"]

def analyze_logic(text):
    text = text.lower()
    score = 0
    found = [w for w in SCAM_KEYWORDS if w in text]
    if len(found) > 0: score += 50
    if score >= 40: return "Fake", 90, f"Detected triggers: {', '.join(found[:3])}"
    return "Real", 70, "No scam patterns found"

@app.route('/', methods=['GET'])
def home(): return "API is running!"

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()
        res, conf, reason = analyze_logic(data.get('text', ''))
        return jsonify({"status": "success", "result": res, "confidence": conf, "reason": reason})
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

@app.route('/predict-image', methods=['POST'])
def predict_image():
    if 'image' not in request.files: return jsonify({"status": "error"}), 400
    try:
        file = request.files['image']
        img = Image.open(file.stream)
        text = pytesseract.image_to_string(img)
        res, conf, reason = analyze_logic(text)
        return jsonify({
            "status": "success", 
            "result": res, 
            "confidence": conf, 
            "reason": reason, 
            "extracted_text": text[:300]
        })
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
