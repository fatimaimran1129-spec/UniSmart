<?php require '../inc.php'; need('teacher'); $id = $_SESSION['u']['id']; $msg = '';
if ($_POST) {
    $f = save_upload('file');
    if ($f === false) $msg = '<p class=err>Invalid file. Only PDF, DOCX or TXT up to 5 MB is allowed.</p>';
    else { q('INSERT INTO assignments(teacher_id,title,description,file) VALUES(?,?,?,?)', [$id, $_POST['title'], $_POST['description'], $f]); $msg = '<p class=ok>Assignment created.</p>'; }
}
head('Assignments'); echo $msg; ?>
<form method="post" enctype="multipart/form-data" class="card"><h3>Upload a new assignment</h3>
<input name="title" placeholder="Title" required><textarea name="description" rows="3" placeholder="Instructions"></textarea>
<label>Attach a file (optional: PDF, DOCX or TXT, max 5 MB)</label><input type="file" name="file" accept=".pdf,.docx,.txt"><button>Create Assignment</button></form>
<table><tr><th>Title</th><th>File</th><th>Date</th><th></th></tr>
<?php foreach (q('SELECT * FROM assignments WHERE teacher_id=? ORDER BY id DESC', [$id])->fetchAll(PDO::FETCH_ASSOC) as $a) echo '<tr><td>' . e($a['title']) . '</td><td>' . file_link($a['file']) . '</td><td>' . $a['created'] . '</td><td><a href="submissions.php?id=' . $a['id'] . '">View submissions</a></td></tr>'; ?></table>
<?php foot();
