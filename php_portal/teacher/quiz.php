<?php require '../inc.php'; need('teacher'); $msg = '';
if ($_POST) {
    $notes = trim($_POST['notes'] ?? ''); $f = save_upload('file');
    if ($f === false) $msg = '<p class=err>Invalid file. Only PDF, DOCX or TXT up to 5 MB is allowed.</p>';
    else {
        if ($f) { $notes = file_text($f); if ($notes === null) $msg = ai_down() . '<p class=err>(Or the file text could not be read.)</p>'; }
        if (!$msg) {
            $r = $notes ? ai('/quiz', ['notes' => $notes, 'n' => (int)$_POST['n']]) : null;
            if (!$notes) $msg = '<p class=err>Please upload a file or paste your notes.</p>';
            elseif (!$r) $msg = ai_down();
            elseif (!$r['questions']) $msg = '<p class=err>No questions could be created. Use longer English sentences.</p>';
            else { q('INSERT INTO quizzes(teacher_id,title,data) VALUES(?,?,?)', [$_SESSION['u']['id'], $_POST['title'], json_encode($r['questions'])]); $msg = '<p class=ok>' . count($r['questions']) . ' questions created.</p>'; }
        }
    }
}
head('AI Quiz Generator'); echo $msg; ?>
<form method="post" enctype="multipart/form-data" class="card"><input name="title" placeholder="Quiz title" required>
<label>Upload lecture notes (PDF, DOCX or TXT, max 5 MB)</label><input type="file" name="file" accept=".pdf,.docx,.txt">
<label>or paste your notes (English)</label><textarea name="notes" rows="6" placeholder="Paste notes here"></textarea>
<label>Number of questions</label><input name="n" type="number" value="5" min="1" max="10"><button>Generate Quiz</button></form>
<?php foreach (q('SELECT * FROM quizzes WHERE teacher_id=? ORDER BY id DESC', [$_SESSION['u']['id']])->fetchAll(PDO::FETCH_ASSOC) as $z) { echo '<div class=card><b>' . e($z['title']) . '</b>'; foreach (json_decode($z['data'], true) as $i => $x) echo '<p>' . ($i + 1) . '. ' . e($x['q']) . '<br><small>Options: ' . e(implode(' | ', $x['options'])) . ' (correct: ' . e($x['options'][$x['correct']]) . ')</small></p>'; echo '</div>'; }
foot();
