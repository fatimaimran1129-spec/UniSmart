<?php require '../inc.php'; need('admin'); head('Admin Dashboard');
foreach (['student' => 'Students', 'teacher' => 'Teachers'] as $r => $l) echo '<div class=stat><b>' . q('SELECT COUNT(*) FROM users WHERE role=?', [$r])->fetchColumn() . '</b>' . $l . '</div>';
echo '<div class=stat><b>' . q('SELECT COUNT(*) FROM complaints')->fetchColumn() . '</b>Complaints</div>';
echo '<div class=card><h3>Complaints by department (chosen by AI)</h3>'; bars(q('SELECT dept,COUNT(*) FROM complaints GROUP BY dept')->fetchAll(PDO::FETCH_NUM)); echo '</div>';
echo '<div class=card><h3>Teacher feedback (sentiment)</h3>'; bars(q('SELECT sentiment,COUNT(*) FROM feedback GROUP BY sentiment')->fetchAll(PDO::FETCH_NUM)); echo '</div>';
foot();
