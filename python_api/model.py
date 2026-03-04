import pandas as pd
import pickle
import re
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression

def clean_text(text):
    text = re.sub(r'\W', ' ', text)
    return text.lower()

# Comprehensive dataset with diverse patterns
data = {
    "text": [
        # --- FAKE / SCAM PATTERNS ---
        "earn money from home pay registration fee to start",
        "send bank details and security deposit for employee ID",
        "urgent job hiring without interview guaranteed selection",
        "data entry job work from home high salary no experience",
        "payment required for training kit and laptop delivery",
        "contact on WhatsApp for immediate joining and offer letter",
        "deposit 5000 rupees as refundable security amount",
        "congratulations you are selected send processing fees",
        "easy work from home 1000 per day send 500 for app",
        "government job direct joining via quota pay commission",
        "bank job selection without exam pay for verification",
        "unclaimed lottery prize or job offer from unknown company",
        "provide OTP or bank account access for payroll setup",
        "hurry limited seats available for this dream job",
        "no interview process direct selection for international role",
        "pay for medical checkup and visa processing for Dubai job",
        "Amazon work from home part time high pay no skills",
        "Telegram group for jobs join now and pay fees",
        "official selection letter but from gmail or outlook email",
        "Urgent Action Required! Don't miss this opportunity guaranteed!",
        "Please respond to this email urgently to guarantee your position",
        "security deposit needed for documentation and background check",
        "Registration fee is mandatory for all new candidates",
        "Job selection based only on your resume no interview needed",
        "Earn 50k per month just by typing images at home",
        "Pay for professional training and certification before joining",
        
        # --- REAL / PROFESSIONAL PATTERNS ---
        "software engineer job in infosys bangalore 5 years experience",
        "hiring web developer with React and Nodejs skills",
        "official hr interview tomorrow at 10 am in corporate office",
        "job responsibilities include managing team and project delivery",
        "technical requirements include SQL Python and Java knowledge",
        "apply via official company portal for the associate role",
        "salary package based on industry standards and experience",
        "we follow a structured recruitment process including assessment",
        "standard background verification will be conducted after offer",
        "no recruitment fees are charged at any stage of the process",
        "senior data scientist role with 8 years of industry experience",
        "customer support executive hiring for night shifts",
        "marketing manager position requiring MBA and 3 years experience",
        "scheduled technical rounds of interview after initial screening",
        "benefits include health insurance and performance bonus",
        "looking for candidates with excellent communication skills",
        "full time position at our Mumbai branch office",
        "project manager role focusing on agile methodology",
        "quality assurance engineer for automation testing project",
        "finance analyst position with CA or CFA qualification",
        "business development executive for B2B sales in Delhi",
        "UX designer role with a strong portfolio in mobile apps",
        "content writer for tech blog with SEO knowledge",
        "human resources recruiter for IT hiring in suburban office",
        "system administrator for managing server infrastructure"
    ],
    "label": [
        "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake",
        "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake",
        "Fake", "Fake", "Fake", "Fake", "Fake", "Fake",
        "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real",
        "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real",
        "Real", "Real", "Real", "Real", "Real"
    ]
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
