<?php require '../inc.php'; need('student'); $zid = (int)($_GET['id'] ?? 0); head('Quiz');
if (!$zid) { foreach (q('SELECT id,title FROM quizzes ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC) as $z) echo '<div class=card><a href="quiz.php?id=' . $z['id'] . '">' . e($z['title']) . '</a></div>'; foot(); exit; }
$qs = json_decode(q('SELECT data FROM quizzes WHERE id=?', [$zid])->fetchColumn(), true);
if ($_POST) { $s = 0; foreach ($qs as $i => $x) if (isset($_POST["q$i"]) && (int)$_POST["q$i"] === $x['correct']) $s++; echo '<div class=card><h3 class=ok>Score: ' . $s . ' / ' . count($qs) . '</h3></div>'; }
echo '<form method="post">'; foreach ($qs as $i => $x) { echo '<div class=card><p>' . ($i + 1) . '. ' . e($x['q']) . '</p>'; foreach ($x['options'] as $k => $o) echo '<label><input type="radio" style="display:inline;width:auto" name="q' . $i . '" value="' . $k . '"> ' . e($o) . '</label><br>'; echo '</div>'; }
echo '<button>Submit Quiz</button></form>'; foot();
