<?php require '../inc.php'; need('student'); $id = $_SESSION['u']['id']; $msg = '';
if ($_POST) { $r = ai('/complaint', ['text' => $_POST['text']]);
    if (!$r) $msg = ai_down(); else { q('INSERT INTO complaints(student_id,text,dept) VALUES(?,?,?)', [$id, $_POST['text'], $r['dept']]); $msg = '<p class=ok>Complaint recorded. The AI routed it to the <b>' . e($r['dept']) . '</b> department.</p>'; } }
head('Complaint Box'); echo $msg; ?>
<form method="post" class="card"><textarea name="text" rows="4" placeholder="Write your complaint here" required></textarea><button>Submit</button></form>
<table><tr><th>Complaint</th><th>Department</th><th>Date</th></tr>
<?php foreach (q('SELECT * FROM complaints WHERE student_id=? ORDER BY id DESC', [$id])->fetchAll(PDO::FETCH_ASSOC) as $c) echo '<tr><td>' . e($c['text']) . '</td><td>' . e($c['dept']) . '</td><td>' . $c['created'] . '</td></tr>'; ?></table>
<?php foot();
