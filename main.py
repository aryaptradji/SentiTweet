import re
import string
import pickle
import numpy as np
import nltk
import joblib
from fastapi import FastAPI
from pydantic import BaseModel
from typing import Union
from deep_translator import GoogleTranslator
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory
from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory, StopWordRemover, ArrayDictionary
from tensorflow.keras.models import load_model # type: ignore

nltk.download("punkt")
nltk.download("stopwords")

app = FastAPI()

class NameClass(BaseModel):
    text: str

# Define preprocessing functions
def cleaningText(text):
    text = re.sub(r"\d+", "", text)  # Hapus angka
    text = re.sub(r"@[\w_]+", "", text)  # Hapus mention (@username)
    text = re.sub(r"#\w+", "", text)  # Hapus hashtag (#)
    text = re.sub(r"\bRT\b", "", text)  # Hapus retweet (RT)
    text = re.sub(r"http\S+", "", text)  # Hapus URL
    text = re.sub(r"[^\w\s]", "", text)  # Hapus tanda baca
    text = re.sub(r"[^a-zA-Z0-9\s]", "", text)  # Hapus karakter non-alphanumeric kecuali spasi
    text = re.sub(r"\s+", " ", text).strip()  # Hapus spasi berlebihan

    return text

def translate_to_indonesian(text):
    try:
        translated = GoogleTranslator(source="auto", target="id").translate(text)
        clean_translated = cleaningText(translated)
        return clean_translated
    except Exception as e:
        print(f"Translation error: {str(e)}")
        return text  # Kembalikan teks asli jika terjadi kesalahan

def casefoldingText(text):
    return text.lower()

def tokenizingText(text):
    return word_tokenize(text)

def filteringText(tokens):
    stop_factory = StopWordRemoverFactory().get_stop_words()  # load default stopword
    more_stopword = [
        "cartier", "kasut", "ni", "ancrit", "yaaa", "yaaaa", "ajaaa", "ya", "aja", "eh", "ehh" "deh", "dah", "pac", "yhcry",
        "ajasii", "la", "weh", "oy", "oyy", "oyyy" "woyyy", "woyy", "woy" "yg", "yang", "drjrt", "nd", "n", "st", "tl", "nah",
        "hah", "lah", "lw", "grgr", "select", "yh", "iy", "gasi", "gasih", "sih", "l", "ha", "no", "tu", "gitu", "pun", "pn",
        "enih", "btw", "hmz",
    ]  # menambahkan stopword tambahan
    stop_words = set(stop_factory + more_stopword)  # menggabungkan stopword dan mengubahnya menjadi set

    # Menghapus stopwords
    filtered_tokens = [token for token in tokens if token not in stop_words]

    return filtered_tokens

def normalisasi(tokens):
    norm = {
        "nggak": "tidak", "gak": "tidak", "ga": "tidak", "g": "tidak", "tak": "tidak", "tdk": "tidak", "klo": "kalau",
        "kalo": "kalau", "yg": "", "y": "", "kynya": "sepertinya", "kyknya": "sepertinya", "kayaknya": "sepertinya",
        "yang": "", "ehh": "", "mmg": "memang", "mjd": "menjadi", "akn": "akan", "jg": "juga", "jugak": "juga",
        "sbnrnya": "sebenarnya", "msh": "masih", "lg": "lagi", "sy": "saya", "kmu": "kamu", "km": "kamu", "dlm": "dalam",
        "dr": "dari", "sm": "sama", "tp": "tapi", "tapiii": "tapi", "krn": "karena", "dg": "dengan", "trs": "terus",
        "aja": "saja", "ae": "saja", "aj": "saja", "jgn": "jangan", "jd": "jadi", "udh": "sudah", "udah": "sudah",
        "kl": "kalo", "org": "orang", "orng": "orang", "dpt": "dapat", "bgt": "banget", "pd": "pada", "kpn": "kapan",
        "plis": "tolong", "pls": "tolong", "aja": "saja", "sbnr": "sebenarnya", "blm": "belum", "tmn": "teman",
        "bgmn": "bagaimana", "anj": "anjing", "trims": "terima kasih", "makasih": "terima kasih", "thx": "terima kasih",
        "tq": "terima kasih", "isriwil": "israel", "palestine": "palestina", "wktu": "waktu", "utk": "untuk", "imo": "menurut saya",
        "fyi": "sebagai informasi", "baguuuss": "bagus", "bgus": "bagus", "rameeeee": "rame", "ovt": "kepikiran",
        "takutt": "takut", "maaff": "maaf", "salty": "kesal", "boyot": "boikot", "boyikot": "boikot", "boycot": "boikot",
        "boycrot": "boikot", "boycott": "boikot", "boycotting": "boikot", "zinist": "zionis", "ayooo": "ayo", "btl": "betul",
        "tauu": "tau", "byk": "banyak", "pmx": "", "amp": "", "xda": "ada",
    }

    normalized_words = []
    for text in tokens:
        words = text.split()
        normalized_text = []
        for word in words:
            if word.lower() in norm:
                normalized_text.append(norm[word.lower()])
            else:
                normalized_text.append(word)
        normalized_words.append(" ".join(normalized_text))
    return normalized_words

def stemmingText(tokens):
    factory = StemmerFactory()
    stemmer = factory.create_stemmer()
    return [stemmer.stem(word) for word in tokens]

def toSentence(list_words):
    return " ".join(list_words)

# Load vectorizer and model
vectorizer = joblib.load('model/tfidf_vectorizer.pkl')
model = load_model('model/best_sentiment_model.h5')

@app.get("/")
def read_root():
    return {"message": "Hello, World!"}

@app.post("/sentiment")
def preprocess_and_predict(ulasan: NameClass):
    review = ulasan.text

    # Preprocess the text
    text = translate_to_indonesian(review)
    text = cleaningText(text)
    text = casefoldingText(text)
    tokens = tokenizingText(text)
    tokens = filteringText(tokens)
    tokens = normalisasi(tokens)
    stemmed_tokens = stemmingText(tokens)
    sentence = toSentence(stemmed_tokens)

    # Transform the text using the vectorizer
    vectorized_text = vectorizer.transform([sentence])

    # Perform prediction
    predictions = model.predict(vectorized_text.toarray())

    # Get the index of the maximum value in each prediction
    predicted_labels = np.argmax(predictions, axis=1)
    
    # Define labels corresponding to indices
    labels = ["negative", "neutral", "positive"]
    
    # Select the label based on prediction result
    predicted_sentiment = labels[predicted_labels[0]]

    return {"sentiment": predicted_sentiment}