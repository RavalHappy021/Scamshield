import pandas as pd
import pickle
import re
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression

def clean_text(text):
    text = re.sub(r'\W', ' ', text)
    return text.lower()

# Comprehensive dataset with 150+ diverse patterns for high accuracy
data = {
    "text": [
        # --- FAKE / SCAM PATTERNS (75+ examples) ---
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
        "we will send you a check to purchase office equipment",
        "buy gift cards and send codes for software license setup",
        "immediate start as personal assistant pay 500 weekly via check",
        "secret shopper position available earn 200 per assignment",
        "must provide SSN and passport copy before first interview",
        "processing fee for background check must be paid upfront",
        "exclusive job offer from celebrity manager pay for access",
        "work 2 hours a day and earn 10,000 per week",
        "no experience required for high-level management role",
        "pay for company laptop insurance before we ship it",
        "hiring urgently for remote role send 100 for ID card",
        "deposit money into this account to activate your profile",
        "investment coordinator role pay 1000 to join the platform",
        "earn commission by transferring funds between accounts",
        "limited time offer: pay 200 for job placement service",
        "guaranteed offshore job pay for travel insurance now",
        "your resume was selected from a database pay for referral",
        "company hiring globally pay for English proficiency test",
        "get paid to post ads on social media send entry fee",
        "representative role needed to receive client payments",
        "crypto trading assistant no experience earn 5% commission",
        "pay for uniform and name tag before your first day",
        "exclusive work-from-home invite pay for starter kit",
        "immediate opening for delivery driver pay for background check",
        "earn from home by filling surveys pay for premium access",
        "high paying job in Canada pay for work permit assistance",
        "pay for mandatory safety training before you can start",
        "your job application is approved pay for final processing",
        "executive assistant for billionaire pay for NDA processing",
        "receive money from clients and send it back to us",
        "package dispatcher role work from home pay for supplies",
        "earn 100 for every email you send no skills needed",
        "pay for cloud storage setup for your remote work",
        "immediate hire for chat support pay for headset shipping",
        "global firm hiring pay for membership to see listings",
        "earn rewards for testing apps pay for developer account",
        "pay for your own background investigation to speed up hire",
        "work as a mystery driver earn 500 per trip pay fee",
        "pay for notary services for your employment contract",
        "secure your spot in the training batch by paying 300",
        "fast track your application pay 50 for priority review",
        "earn big by inviting friends to this job portal pay fee",
        "pay for digital signature setup for your job offer",
        "immediate joining for warehouse role pay for safety boots",
        "earn passive income by renting your computer pay for setup",
        "pay for administrative handling of your file",
        "exclusive internship pay for the learning materials",
        "get a job in a top bank pay for the interview slot",
        "pay for your own drug test before we send the contract",

        # --- REAL / PROFESSIONAL PATTERNS (75+ examples) ---
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
        "system administrator for managing server infrastructure",
        "registered nurse position at city hospital 3 years exp",
        "high school math teacher role in public school district",
        "civil engineer for bridge construction project in Pune",
        "content moderator for social media platform 24/7 shifts",
        "sales associate for luxury retail store in shopping mall",
        "accountant position with knowledge of Tally and GST",
        "legal assistant for law firm in Mumbai high court",
        "receptionist role for dental clinic 10am to 6pm",
        "mechanical engineer for automotive plant in Chennai",
        "chef de partie for 5-star hotel restaurant in Goa",
        "warehouse supervisor with 5 years in logistics management",
        "security officer for corporate park night shift only",
        "delivery driver for e-commerce company valid license req",
        "operations manager for manufacturing unit in Gujarat",
        "graphic designer with proficiency in Adobe Creative Suite",
        "data analyst for E-commerce firm using Excel and PowerBI",
        "network engineer with CCNA certification 2 years exp",
        "office administrator for real estate company full time",
        "telesales executive for insurance company in Hyderabad",
        "web content uploader for news portal night shift",
        "front office executive for IT park in Bangalore",
        "laboratory technician for diagnostic center B.Sc req",
        "social media manager for fashion brand in Mumbai",
        "inventory clerk for wholesale market 8am to 5pm",
        "driver for private company 10 years experience",
        "electrical technician for residential building maintenance",
        "waiter for fine dining restaurant in South Delhi",
        "content editor for publishing house in Kolkata",
        "backend developer with Go or Rust experience",
        "cloud architect for AWS and Azure infrastructure",
        "cybersecurity analyst with CISSP certification",
        "mobile app developer for iOS and Android platforms",
        "devops engineer with Kubernetes and Docker knowledge",
        "blockchain developer for fintech startup in Pune",
        "embedded systems engineer for IoT project",
        "machine learning engineer for computer vision tasks",
        "full stack developer with Vue and Django skills",
        "site reliability engineer for high traffic portal",
        "technical writer for API documentation 3 years exp",
        "product manager for SaaS product in early stage",
        "scrum master with PSM I certification for 2 years",
        "database administrator for Oracle and MySQL systems",
        "frontend engineer with Tailwind and Nextjs skills",
        "ruby on rails developer for legacy system migration",
        "laravel developer for custom ERP development",
        "wordpress developer for agency projects full time",
        "shopify developer for e-commerce store setup",
        "magento developer for enterprise scale platforms",
        "react native developer for cross platform apps",
        "ios developer with swift and objective c skills"
    ],
    "label": [
        # 75 Fake
        "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake",
        "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake",
        "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake",
        "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake",
        "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake",
        "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake",
        "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake", "Fake",
        "Fake", "Fake", "Fake", "Fake", "Fake",
        # 75 Real
        "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real",
        "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real",
        "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real",
        "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real",
        "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real", "Real",
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
