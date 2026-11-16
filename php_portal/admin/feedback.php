<?php require '../inc.php'; need('admin'); head('Teacher Feedback Report'); ?>
<table><tr><th>Teacher</th><th>Feedback</th><th>Sentiment (AI)</th></tr>
<?php foreach (q('SELECT f.*,t.name FROM feedback f JOIN users t ON t.id=f.teacher_id ORDER BY f.id DESC')->fetchAll(PDO::FETCH_ASSOC) as $f) echo '<tr><td>' . e($f['name']) . '</td><td>' . e($f['text']) . '</td><td>' . e($f['sentiment']) . '</td></tr>'; ?></table>
<?php foot();
