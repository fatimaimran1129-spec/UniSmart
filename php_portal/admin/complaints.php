<?php require '../inc.php'; need('admin'); head('All Complaints'); ?>
<table><tr><th>Student</th><th>Complaint</th><th>Department (AI)</th><th>Date</th></tr>
<?php foreach (q('SELECT c.*,u.name FROM complaints c JOIN users u ON u.id=c.student_id ORDER BY c.id DESC')->fetchAll(PDO::FETCH_ASSOC) as $c) echo '<tr><td>' . e($c['name']) . '</td><td>' . e($c['text']) . '</td><td>' . e($c['dept']) . '</td><td>' . $c['created'] . '</td></tr>'; ?></table>
<?php foot();
