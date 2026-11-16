"""UniSmart AI module (scikit-learn only, no heavy model downloads)."""
import base64, csv, html, io, json, random, re, zipfile
from pathlib import Path
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.pipeline import Pipeline

D = Path(__file__).parent / "data"


def _train(file):
    rows = list(csv.DictReader(open(D / file, encoding="utf-8")))
    p = Pipeline([("t", TfidfVectorizer(ngram_range=(1, 2), sublinear_tf=True)),
                  ("c", LogisticRegression(max_iter=2000, C=10))])
    return p.fit([r["text"] for r in rows], [r["label"] for r in rows])


COMPLAINT, SENT = _train("complaints.csv"), _train("feedback.csv")
FAQ = json.load(open(D / "faq.json", encoding="utf-8"))
_fv = TfidfVectorizer(ngram_range=(1, 2), stop_words="english")
_fm = _fv.fit_transform([f["q"] for f in FAQ])


def classify(model, text):
    pr = model.predict_proba([text])[0]
    i = int(pr.argmax())
    return str(model.classes_[i]), round(float(pr[i]), 2)


def plagiarism(items):
    """Similarity (0-100%) of each submission with the most similar other one."""
    if len(items) < 2:
        return [{"id": i["id"], "score": 0, "with": ""} for i in items]
    s = cosine_similarity(TfidfVectorizer(ngram_range=(1, 2)).fit_transform([i["text"] for i in items]))
    out = []
    for k, i in enumerate(items):
        s[k][k] = 0
        j = int(s[k].argmax())
        out.append({"id": i["id"], "score": round(float(s[k][j]) * 100), "with": f"Submission #{items[j]['id']}"})
    return out


def grade(model_answer, answer, marks=10):
    m = TfidfVectorizer().fit_transform([model_answer, answer])
    return round(float(cosine_similarity(m[0], m[1])[0][0]) * marks, 1)


STOP = set("about which their there these those would could should because between through during before after other while where being have been".split())


def quiz(notes, n=5):
    """Fill-in-the-blank MCQs: hide the longest keyword of a sentence and add 3 distractors."""
    sents = [s.strip() for s in re.split(r"(?<=[.!?])\s+", notes) if len(s.split()) >= 6]
    items = []
    for s in sents:
        w = [x for x in re.findall(r"[A-Za-z]{6,}", s) if x.lower() not in STOP]
        if w:
            items.append((s, max(w, key=len)))
    pool = list({a for _, a in items})
    out = []
    for s, a in items[:n]:
        d = random.sample([p for p in pool if p != a], min(3, len(pool) - 1))
        o = d + [a]
        random.shuffle(o)
        out.append({"q": re.sub(re.escape(a), "_____", s, count=1), "options": o, "correct": o.index(a)})
    return out


def chat(text):
    s = cosine_similarity(_fv.transform([text]), _fm)[0]
    i = int(s.argmax())
    return FAQ[i]["a"] if s[i] > 0.2 else "Sorry, I do not know the answer to that. Please contact the admin office."


def extract(filename, data_b64):
    """Read text from an uploaded .txt, .docx or .pdf file."""
    raw, ext = base64.b64decode(data_b64), filename.lower().rsplit(".", 1)[-1]
    if ext == "txt":
        return raw.decode("utf-8", "ignore")
    if ext == "docx":
        with zipfile.ZipFile(io.BytesIO(raw)) as z:
            xml = z.read("word/document.xml").decode("utf-8", "ignore")
        return html.unescape(re.sub(r"<[^>]+>", "", xml.replace("</w:p>", "\n")))
    if ext == "pdf":
        from pypdf import PdfReader   # pip install pypdf
        return "\n".join((p.extract_text() or "") for p in PdfReader(io.BytesIO(raw)).pages)
    raise ValueError("Unsupported file type")
