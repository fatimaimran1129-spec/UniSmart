<?php require '../inc.php'; need('teacher'); $id = (int)$_GET['id']; $down = false;
if (isset($_POST['check'])) {
    $rows = q('SELECT id,text FROM submissions WHERE assignment_id=?', [$id])->fetchAll(PDO::FETCH_ASSOC);
    $r = ai('/plagiarism', ['items' => $rows]);
    if ($r) foreach ($r['results'] as $x) q('UPDATE submissions SET plag_score=?,plag_with=? WHERE id=?', [$x['score'], $x['with'], $x['id']]); else $down = true;
}
head('Submissions and Plagiarism Check'); if ($down) echo ai_down(); ?>
<form method="post"><button name="check" value="1">Run AI plagiarism check</button></form><br>
<table><tr><th>#</th><th>Student</th><th>Answer</th><th>File</th><th>Similarity %</th><th>Most similar to</th></tr>
<?php foreach (q('SELECT s.*,u.name FROM submissions s JOIN users u ON u.id=s.student_id WHERE assignment_id=?', [$id])->fetchAll(PDO::FETCH_ASSOC) as $s) {
    $hi = $s['plag_score'] !== null && $s['plag_score'] >= 70;
    echo '<tr><td>' . $s['id'] . '</td><td>' . e($s['name']) . '</td><td>' . e(mb_substr($s['text'], 0, 120)) . '</td><td>' . file_link($s['file']) . '</td><td' . ($hi ? ' class=err' : '') . '>' . ($s['plag_score'] ?? '-') . '</td><td>' . e($s['plag_with']) . '</td></tr>'; } ?></table>
<?php foot();
