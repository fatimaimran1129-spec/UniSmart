<?php require '../inc.php'; need('student'); $msg = '';
if ($_POST) { $r = ai('/sentiment', ['text' => $_POST['text']]);
    if (!$r) $msg = ai_down(); else { q('INSERT INTO feedback(student_id,teacher_id,text,sentiment) VALUES(?,?,?,?)', [$_SESSION['u']['id'], (int)$_POST['tid'], $_POST['text'], $r['sentiment']]); $msg = '<p class=ok>Thank you! Your feedback was detected as <b>' . e($r['sentiment']) . '</b>.</p>'; } }
head('Teacher Feedback'); echo $msg; ?>
<form method="post" class="card"><select name="tid"><?php foreach (q('SELECT id,name FROM users WHERE role="teacher"')->fetchAll(PDO::FETCH_ASSOC) as $t) echo '<option value="' . $t['id'] . '">' . e($t['name']) . '</option>'; ?></select>
<textarea name="text" rows="4" placeholder="Write your feedback about the teacher" required></textarea><button>Submit</button></form>
<?php foot();
