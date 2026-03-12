import pandas as pd
import pickle
import re
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression

def clean_text(text):
    text = re.sub(r'\W', ' ', text)
    return text.lower()

# Expanded dataset (100+ examples)
data = {
    "text": [
        # --- FAKE JOBS (Scams) ---
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

        # --- REAL JOBS (Legitimate) ---
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
    ],
    "label": ["Fake"] * 50 + ["Real"] * 50
}

df = pd.DataFrame(data)
df["text"] = df["text"].apply(clean_text)

X = df["text"]
y = df["label"]

vectorizer = TfidfVectorizer(stop_words='english')
X_vec = vectorizer.fit_transform(X)

model = LogisticRegression()
model.fit(X_vec, y)

pickle.dump(model, open("model.pkl","wb"))
pickle.dump(vectorizer, open("vectorizer.pkl","wb"))

print("Model trained successfully")
