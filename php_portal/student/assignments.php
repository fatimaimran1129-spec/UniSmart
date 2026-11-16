<?php require '../inc.php'; need('student'); $id = $_SESSION['u']['id']; $msg = '';
if ($_POST) {
    $text = trim($_POST['text'] ?? ''); $f = save_upload('file');
    if ($f === false) $msg = '<p class=err>Invalid file. Only PDF, DOCX or TXT up to 5 MB is allowed.</p>';
    else {
        if ($f) { $text = file_text($f); if ($text === null) { $msg = ai_down() . '<p class=err>(Or the file text could not be read.)</p>'; } }
        if (!$msg && !$text) $msg = '<p class=err>Please write an answer or upload a file.</p>';
        if (!$msg) { q('INSERT INTO submissions(assignment_id,student_id,text,file) VALUES(?,?,?,?)', [(int)$_POST['aid'], $id, $text, $f ?: null]); $msg = '<p class=ok>Assignment submitted.</p>'; }
    }
}
head('Assignments'); echo $msg;
foreach (q('SELECT a.*,t.name tn,(SELECT COUNT(*) FROM submissions s WHERE s.assignment_id=a.id AND s.student_id=?) done FROM assignments a JOIN users t ON t.id=a.teacher_id ORDER BY a.id DESC', [$id])->fetchAll(PDO::FETCH_ASSOC) as $a) {
    echo '<div class=card><b>' . e($a['title']) . '</b> <small>(' . e($a['tn']) . ')</small><p>' . e($a['description']) . '</p><p>' . file_link($a['file']) . '</p>';
    echo $a['done'] ? '<p class=ok>Already submitted.</p>' : '<form method="post" enctype="multipart/form-data"><input type="hidden" name="aid" value="' . $a['id'] . '"><textarea name="text" rows="4" placeholder="Write your answer here"></textarea><label>or upload your file (PDF, DOCX or TXT, max 5 MB)</label><input type="file" name="file" accept=".pdf,.docx,.txt"><button>Submit</button></form>';
    echo '</div>'; }
foot();
