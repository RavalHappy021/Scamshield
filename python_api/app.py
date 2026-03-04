from flask import Flask, request, jsonify
import pickle
import re
import pytesseract
from PIL import Image
import os

app = Flask(__name__)

# Optional: Set tesseract path if it's not in your PATH
# Set tesseract path
tesseract_path = os.getenv('TESSERACT_PATH')
if not tesseract_path:
    if os.name == 'posix':
        tesseract_path = '/usr/bin/tesseract'
    else:
        tesseract_path = r'C:\Program Files\Tesseract-OCR\tesseract.exe'

# Only force the path if we are on Windows or if TESSERACT_PATH is explicitly set
if os.getenv('TESSERACT_PATH') or os.name != 'posix':
    if os.path.exists(tesseract_path):
        pytesseract.pytesseract.tesseract_cmd = tesseract_path

# Load trained model and vectorizer
model = pickle.load(open("model.pkl", "rb"))
vectorizer = pickle.load(open("vectorizer.pkl", "rb"))

@app.route("/", methods=["GET"])
def home():
    return jsonify({
        "status": "online",
        "message": "ScamShield AI API is running",
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
    data = request.json
    job_text = data["text"]

    clean = clean_text(job_text)
    vec = vectorizer.transform([clean])

    prediction = model.predict(vec)[0]
    confidence = model.predict_proba(vec).max() * 100
    reason = get_reason(job_text, prediction)

    return jsonify({
        "result": prediction,
        "confidence": round(confidence, 2),
        "reason": reason
    })

@app.route("/predict-image", methods=["POST"])
def predict_image():
    if 'image' not in request.files:
        return jsonify({"status": "error", "message": "No image uploaded"}), 400
    
    file = request.files['image']
    if file.filename == '':
        return jsonify({"status": "error", "message": "No image selected"}), 400

    try:
        # Load image
        img = Image.open(file)
        
        # Optimize image: Convert to RGB and resize if too large
        if img.mode != 'RGB':
            img = img.convert('RGB')
        
        max_size = 1800
        if max(img.size) > max_size:
            ratio = max_size / max(img.size)
            new_size = (int(img.size[0] * ratio), int(img.size[1] * ratio))
            img = img.resize(new_size, Image.Resampling.LANCZOS)
            print(f"Resized image to {new_size}")

        # Perform OCR
        extracted_text = pytesseract.image_to_string(img)
        
        if not extracted_text.strip():
            return jsonify({
                "status": "error", 
                "message": "The AI could not read any text from the image. Please upload a clearer screenshot/poster."
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
        import traceback
        error_details = traceback.format_exc()
        print(f"Prediction Error:\n{error_details}")
        return jsonify({
            "status": "error", 
            "message": f"Server Error: {str(e)}",
            "details": error_details
        }), 500

if __name__ == "__main__":
    app.run(debug=True)
