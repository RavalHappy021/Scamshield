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

# Load trained model and vectorizer
model = joblib.load("model.pkl")
vectorizer = joblib.load("vectorizer.pkl")

# --- 100+ TRAINING DATA SAMPLES (Reference) ---
TRAINING_DATA = [
    # FAKE JOBS (Scams)
    "earn money from home pay registration fee", "send bank details for job", "urgent job without interview",
    "work from home earn 500 dollars daily no experience", "whatsapp us for quick job", "telegram job offer earn lakhs",
    "pay security deposit for laptop", "buy training kit to start work", "processing fee required for visa",
    "high salary low work hours data entry", "simple typing job earn big", "copy paste job 5000 per week",
    "government job offer without exam", "official selection letter pay fee", "amazon part time work from mobile",
    "flipkart online work earn rewards", "investment needed for business role", "personal assistant needed pay for background check",
    "international job offer immediate joining", "lottery winner job vacancy", "mystery shopper pay for first item",
    "app tester earn while playing", "review products for money pay for account", "survey job high payment per click",
    "no interview needed start today", "send money to HR for uniform", "medical coding job from home pay for software",
    "bank employee recruitment pay for portal", "airline ground staff vacancy pay for training", "cruise ship job offer pay for docs",
    "remote customer support pay for headset", "virtual assistant job pay for training", "digital marketing offer pay for tools",
    "unlimited income potential no skills", "be your own boss join our team pay fee", "network marketing job reach targets",
    "referral based income join group", "exclusive job portal access pay monthly", "VIP recruitment services pay upfront",
    "guaranteed job placement pay commission", "loan officer job pay for documentation", "real estate agent work pay for leads",
    "direct recruitment by MD pay for verification", "executive role no qualification needed", "overseas opportunity pay for insurance",
    "travel agent work from home pay for license", "insurance agent role pay for exam fee", "tax consultant job pay for portal",
    "easy money making scheme join now", "wealth creation opportunity pay member fee",

    # REAL JOBS (Legitimate)
    "software engineer job in infosys", "company hiring web developer", "official hr interview tomorrow",
    "senior java developer at google bangalore", "frontend engineer vacancy at react startup", "backend dev job remote us",
    "full stack developer role mumbai with competitive salary", "ui ux designer internship at adobe", "data scientist position at microsoft",
    "python developer for AI research team", "qa automation engineer at TCS", "devops engineer job at amazon web services",
    "cloud architect role at google cloud", "mobile app developer hiring flutter", "cybersecurity analyst job at hcl",
    "database administrator vacancy at oracie", "system analyst role at accenture", "project manager job at deloitte",
    "business analyst position at kpmg", "marketing coordinator role at coca-cola", "sales executive job at samsung",
    "customer success manager at salesforce", "content writer vacancy at medium", "graphic designer job at canva",
    "video editor role at netflix", "social media manager at spotify", "human resources assistant at meta",
    "office manager job at local clinic", "receptionist vacancy at hilton", "administrative assistant role at university",
    "operations manager job at fedex", "logistics coordinator at dhl", "supply chain analyst at tesla",
    "financial analyst role at goldman sachs", "accountant vacancy at local firm", "auditor job at pwc",
    "legal assistant position at law firm", "paralegal role at corporate office", "research assistant at mit",
    "teaching assistant job at local school", "librarian vacancy at public library", "nurse practitioner job at hospital",
    "medical assistant role at health center", "pharmacist vacancy at cvs", "physical therapist job at clinic",
    "engineer at construction firm", "architect role at urban planning", "civil engineer vacancy at government",
    "mechanical engineer job at ford", "electrical engineer role at siemens"
]

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
