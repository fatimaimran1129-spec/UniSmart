<?php require '../inc.php'; need('student'); $id = $_SESSION['u']['id']; head('Student Dashboard');
echo '<div class=stat><b>' . q('SELECT COUNT(*) FROM assignments')->fetchColumn() . '</b>Assignments</div>';
echo '<div class=stat><b>' . q('SELECT COUNT(*) FROM submissions WHERE student_id=?', [$id])->fetchColumn() . '</b>My submissions</div>';
echo '<div class=stat><b>' . q('SELECT COUNT(*) FROM quizzes')->fetchColumn() . '</b>Quizzes</div>';
echo '<div class=stat><b>' . q('SELECT COUNT(*) FROM complaints WHERE student_id=?', [$id])->fetchColumn() . '</b>My complaints</div>'; foot();
