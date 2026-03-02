import pandas as pd
import pickle
import re
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression

def clean_text(text):
    text = re.sub(r'\W', ' ', text)
    return text.lower()

# Sample dataset (you can later load csv)
data = {
    "text": [
        "earn money from home pay registration fee",
        "send bank details for job",
        "urgent job without interview",
        "software engineer job in infosys",
        "company hiring web developer",
        "official hr interview tomorrow"
    ],
    "label": ["Fake","Fake","Fake","Real","Real","Real"]
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
