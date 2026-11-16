# UniSmart: AI-Based University Portal

A university portal with three roles (Admin, Teacher, Student) and AI features:
plagiarism detection, quiz generation from uploaded notes, complaint routing, teacher-feedback sentiment and a university chatbot.

## How to run (XAMPP)
1. Put the folder at `C:\xampp\htdocs\UniSmart\` (so you have `php_portal` and `ai_service` inside it). Start Apache and MySQL.
2. Open `http://localhost/UniSmart/php_portal/setup.php` once. It creates the database `unismart`, the tables and 3 demo users
   (it also upgrades an older UniSmart database, so run it again after updating):
   - admin@uni.com / admin123, teacher@uni.com / teacher123, student@uni.com / student123
3. Start the AI service:

       cd C:\xampp\htdocs\UniSmart\ai_service
       pip install -r ..\requirements.txt
       uvicorn app:app --port 8001

4. Open `http://localhost/UniSmart/php_portal/` and log in.
   (If you get a curl error, remove the `;` before `extension=curl` in `C:\xampp\php\php.ini` and restart Apache.)

## How uploading works
**Assignments**
1. Teacher: *Assignments* page > enter a title and instructions, optionally attach a PDF/DOCX/TXT file > *Create Assignment*.
2. Student: *Assignments* page > download the teacher's file if any > write the answer in the box **or** upload a PDF/DOCX/TXT file > *Submit*.
3. For an uploaded answer the AI service reads the text inside the file and saves it, so plagiarism checking works on files too.
4. Teacher: *Assignments* > *View submissions* > *Run AI plagiarism check*. Answers with 70% or more similarity are shown in red, together with the submission they match.

**Quizzes**
1. Teacher: *Quiz Generator* page > enter a title > **upload lecture notes (PDF/DOCX/TXT)** or paste them > choose the number of questions > *Generate Quiz*.
2. The AI turns the notes into multiple-choice questions and saves the quiz.
3. Student: *Quiz* page > choose a quiz > answer > *Submit Quiz* to see the score.

Rules: allowed types are PDF, DOCX and TXT, maximum 5 MB. Files are stored in `php_portal/uploads/` under random names.
Scanned (image-only) PDFs contain no text, so they cannot be read. Notes should be in English.

## Pages (17)
Common: login, register, logout. Admin: dashboard, users, complaints, feedback report.
Teacher: dashboard, assignments, submissions, quiz generator. Student: dashboard, assignments, quiz, complaint box, teacher feedback, chatbot.

## AI features
| Feature | Method |
|---|---|
| Plagiarism | TF-IDF vectors + cosine similarity between submissions |
| Complaint routing | TF-IDF + Logistic Regression (Academic, Hostel, Transport, Library, Fee, IT) |
| Feedback sentiment | TF-IDF + Logistic Regression (positive / negative) |
| Quiz generator | Hides a keyword of each sentence and adds 3 distractors |
| Chatbot | Matches the question with `ai_service/data/faq.json` |
| File reading | `.txt` direct, `.docx` via XML, `.pdf` via pypdf |

## Notes
- The training data is small (30 complaints, 16 feedback). Add more rows to `ai_service/data/*.csv` for better accuracy.
- The chatbot answers in `faq.json` are samples: edit them for your university.
- `/grade` (answer grading) exists in the AI service, but there is no page for it yet.
