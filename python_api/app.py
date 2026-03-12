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
# --- 350+ TRAINING DATA SAMPLES (Reference) ---
TRAINING_DATA = [
    # FAKE JOBS (175 total)
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
    "part time online job without investment but pay portal fee", "data entry work from home no experience 1000 per hour",
    "earn money by liking youtube videos", "instagram influencer handler job pay for account verification",
    "global job opportunity pay for work permit processing", "fast track recruitment pay for priority consideration",
    "work from home typing jobs daily payout pay for software", "simple form filling job no skills 3000 weekly",
    "be a mystery reviewer pay for products we refund later", "test gaming apps earn rewards pay for activation",
    "official hr department hiring pay for identity check", "laptop provided for work from home pay for shipping",
    "earn passive income with our job system pay entry fee", "no qualifications needed high salary remote work",
    "immediate vacancy for administrative role pay for vetting", "personal driver job high pay pay for background check",
    "security guard recruitment pay for training and uniform", "clerical staff vacancy pay for application form",
    "retail assistant job pay for employee ID", "warehouse worker recruitment pay for safety gear",
    "delivery driver job pay for route access", "customer care job from home pay for dialer software",
    "tech support role pay for tools and setup", "social media moderator job pay for training portal",
    "content creator job earn while you sleep pay fee", "ad clicking job earn dollars daily",
    "survey participation job pay for member card", "recruitment agency fee for guaranteed job placement",
    "job offer from abroad pay for legal fees and visa", "canadian work permit job pay for processing",
    "australian farm work job pay for document verification", "dubai oil field job pay for medical clearance",
    "singapore hotel job pay for lodging deposit", "london restaurant job pay for food handlers license",
    "work on cruise ships high pay pay for maritime docs", "airline cabin crew job pay for training academy",
    "be a secret shopper for luxury brands pay for initial buy", "product tester job keep products pay for delivery",
    "app review job 5 dollars per review pay for account", "stock broker job pay for license and training",
    "financial advisor role pay for leads and software", "insurance claim processor job pay for portal",
    "medical transcription job pay for foot pedal and software", "legal researcher job pay for database access",
    "freelancing gig pay for premium membership to bid", "remote data analyst pay for specialized software",
    "graphic design job test pay for stock images", "video editing job test pay for assets",
    "software beta tester job pay for access", "web developer intern pay for mentorship",
    "digital marketer role pay for ad budget", "virtual event coordinator pay for platform access",
    "social media manager role pay for scheduler tools", "copywriter job pay for plagiarism checker",
    "editor role pay for style guide access", "translator job pay for dictionary access",
    "voice over artist job pay for studio time", "model recruitment pay for portfolio shoot",
    "actor casting job pay for registration", "extra on movie set pay for agency fee",
    "mystery box reviewer pay for first box", "jewelry tester pay for insurance",
    "cosmetics reviewer pay for shipping", "gadget tester pay for security",
    "book reviewer pay for first copy", "podcast editor job pay for software",
    "email marketing assistant pay for list access", "seo specialist role pay for keyword tools",
    "customer success role pay for headset", "technical writer role pay for documentation tools",
    "quality assurance role pay for testing tools", "hr assistant role pay for portal access",
    "data entry clerk role pay for software license", "receptionist role pay for uniform fee",
    "sales role pay for leads database", "marketing role pay for branding kit",
    "business development role pay for partnership access", "consultant role pay for certification",
    "coach role pay for training manual", "tutor role pay for online portal",
    "translator role pay for software access", "content editor role pay for software",
    "copy editor role pay for style guide", "proofreader role pay for software",
    "blogger role pay for hosting fee", "vlogger role pay for editing software",
    "social media influencer role pay for verification", "brand ambassador role pay for uniform",
    "event staff role pay for training fee", "promoters role pay for marketing materials",
    "retail staff role pay for identity card", "servers role pay for uniform and training",
    "hostess role pay for registration fee", "hotel staff role pay for orientation",
    "travel agent role pay for ticketing system", "flight attendant role pay for safety training",
    "ground crew role pay for security badge", "security staff role pay for uniform",
    "it support role pay for tools and equipment", "network admin role pay for certs",
    "help desk role pay for remote access tools", "data analyst role pay for software",
    "researcher role pay for database access", "librarian role pay for training",
    "teacher role pay for portal and material", "professor role pay for academic access",
    "instructor role pay for certification", "guide role pay for licensing",
    "driver role pay for vehicle registration", "delivery role pay for bag and uniform",
    "courier role pay for tracking software", "packer role pay for safety gear",
    "stocker role pay for uniform fee", "loader role pay for safety training",
    "mechanic role pay for tools", "technician role pay for equipment",
    "electrician role pay for licensing", "plumber role pay for tools",
    "carpenter role pay for equipment", "painter role pay for tools",
    "landscaper role pay for uniform", "gardener role pay for tools",
    "cleaner role pay for equipment", "janitor role pay for uniform",

    # REAL JOBS (175 total)
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
    "mechanical engineer job at ford", "electrical engineer role at siemens",
    "senior data analyst for retail chain with SQL experience", "junior project coordinator at construction firm",
    "account manager for advertising agency in london", "customer relations specialist for bank branch",
    "network infrastructure engineer for telecom company", "supply chain specialist for manufacturing plant",
    "digital content strategist for news portal", "environmental consultant for government agency",
    "healthcare administrator for city hospital", "primary school teacher for public district",
    "university lecturer in computer science", "legal counsel for tech corporation",
    "site engineer for civil projects in mumbai", "sales manager for pharmaceutical company",
    "operations executive for logistics startup", "product manager for fintech application",
    "blockchain developer for crypto exchange", "embedded systems engineer for automotive R&D",
    "warehouse supervisor for e-commerce fulfillment center", "chef de partie for 5-star hotel kitchen",
    "human resources manager for multinational firm", "public relations officer for non-profit",
    "technical support specialist for software company", "mechanical design engineer for aerospace firm",
    "financial controller for energy sector", "procurement officer for global supply chain",
    "marketing manager for consumer electronics brand", "quality control inspector for food processing",
    "research scientist for pharmaceutical lab", "biomedical engineer for medical device company",
    "urban planner for municipal government", "social worker for community outreach program",
    "occupational therapist for rehabilitation center", "speech pathologist for school district",
    "veterinarian for animal hospital", "agriculture specialist for farm research",
    "marine biologist for oceanographic institute", "astrophysicist for space agency",
    "chemical engineer for petrochem plant", "industrial designer for consumer goods",
    "structural engineer for bridge projects", "geotechnical engineer for mining company",
    "flight instructor for aviation school", "air traffic controller for international airport",
    "meteorologist for national weather service", "oceanographer for research vessel",
    "statistician for government census bureau", "actuary for insurance corporation",
    "economist for central bank", "sociologist for research foundation",
    "psychologist for mental health clinic", "counselor for university student services",
    "interior designer for commercial architecture firm", "fashion designer for clothing brand",
    "textile engineer for garment manufacturing", "production assistant for television studio",
    "sound engineer for music recording studio", "lighting technician for theater production",
    "copy editor for book publishing house", "editorial assistant for lifestyle magazine",
    "journalist for national news agency", "photographer for media company",
    "illustrator for children's book publisher", "animator for film studio",
    "game designer for mobile gaming company", "user experience researcher for e-commerce site",
    "accessibility specialist for tech firm", "database developer for healthcare provider",
    "security architect for financial institution", "network analyst for educational network",
    "solution architect for enterprise software", "it manager for mid-sized business",
    "compliance officer for corporate governance", "internal auditor for utility company",
    "tax specialist for accounting firm", "payroll coordinator for large employer",
    "benefits administrator for human resources", "recruiter for staffing agency",
    "training specialist for corporate learning", "diversity officer for inclusive workplace",
    "facilities manager for office complex", "property manager for residential building",
    "leasing agent for commercial real estate", "investment banker for brokerage firm",
    "wealth manager for private clients", "trader for investment fund",
    "risk analyst for banking sector", "quantitative analyst for hedge fund",
    "loan underwriter for mortgage company", "credit analyst for commercial lending",
    "business development manager for b2b services", "strategic planner for corporate strategy",
    "brand manager for household products", "media buyer for advertising firm",
    "creative director for marketing agency", "art director for branding studio",
    "account executive for client services", "sales associate for retail brand",
    "merchandiser for fashion retailer", "store manager for flagship location",
    "district manager for restaurant chain", "franchise coordinator for food service",
    "executive chef for fine dining restaurant", "pastry chef for bakery and cafe",
    "sommelier for upscale dining", "maitre d for luxury hotel",
    "event planner for corporate conferences", "wedding coordinator for event studio",
    "travel consultant for boutique agency", "concierge for boutique hotel",
    "tour guide for historical site", "museum curator for art gallery",
    "archivist for historical society", "registrar for academic institution",
    "admissions officer for private college", "student advisor for higher education",
    "career counselor for vocational school", "grant writer for non-profit organization",
    "fundraising coordinator for charity", "volunteer manager for community service",
    "executive director for foundation", "program manager for international aid",
    "policy analyst for think tank", "government relations officer for corporation"
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
