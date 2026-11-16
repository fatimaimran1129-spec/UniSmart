"""Run:  uvicorn app:app --port 8001"""
from typing import List
from fastapi import FastAPI
from pydantic import BaseModel
import ai

app = FastAPI(title="UniSmart AI")


class Item(BaseModel):
    id: int
    text: str


class Plag(BaseModel):
    items: List[Item]


class Txt(BaseModel):
    text: str


class Grade(BaseModel):
    model_answer: str
    answer: str
    marks: int = 10


class Quiz(BaseModel):
    notes: str
    n: int = 5


class File(BaseModel):
    filename: str
    data: str


@app.post("/plagiarism")
def plag(b: Plag): return {"results": ai.plagiarism([i.dict() for i in b.items])}


@app.post("/grade")
def grade(b: Grade): return {"score": ai.grade(b.model_answer, b.answer, b.marks)}


@app.post("/complaint")
def complaint(b: Txt):
    d, c = ai.classify(ai.COMPLAINT, b.text); return {"dept": d, "confidence": c}


@app.post("/sentiment")
def sentiment(b: Txt):
    s, c = ai.classify(ai.SENT, b.text); return {"sentiment": s, "confidence": c}


@app.post("/quiz")
def quiz(b: Quiz): return {"questions": ai.quiz(b.notes, b.n)}


@app.post("/chat")
def chat(b: Txt): return {"reply": ai.chat(b.text)}


@app.post("/extract")
def extract(b: File):
    try:
        return {"text": ai.extract(b.filename, b.data)}
    except Exception as e:
        return {"text": None, "error": str(e)}
