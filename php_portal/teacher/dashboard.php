<?php require '../inc.php'; need('teacher'); $id = $_SESSION['u']['id']; head('Teacher Dashboard');
echo '<div class=stat><b>' . q('SELECT COUNT(*) FROM assignments WHERE teacher_id=?', [$id])->fetchColumn() . '</b>Assignments</div>';
echo '<div class=stat><b>' . q('SELECT COUNT(*) FROM submissions s JOIN assignments a ON a.id=s.assignment_id WHERE a.teacher_id=?', [$id])->fetchColumn() . '</b>Submissions</div>';
echo '<div class=stat><b>' . q('SELECT COUNT(*) FROM quizzes WHERE teacher_id=?', [$id])->fetchColumn() . '</b>Quizzes</div>';
echo '<div class=card><h3>Feedback about me</h3>'; bars(q('SELECT sentiment,COUNT(*) FROM feedback WHERE teacher_id=? GROUP BY sentiment', [$id])->fetchAll(PDO::FETCH_NUM)); echo '</div>'; foot();
