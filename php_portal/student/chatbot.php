<?php require '../inc.php'; need('student'); $a = '';
if ($_POST) { $r = ai('/chat', ['text' => $_POST['text']]); $a = $r ? '<div class=card><b>You:</b> ' . e($_POST['text']) . '<br><br><b>Bot:</b> ' . e($r['reply']) . '</div>' : ai_down(); }
head('University Chatbot'); echo $a; ?>
<form method="post" class="card"><input name="text" placeholder="Ask about fees, admission, library or datesheet" required><button>Ask</button></form>
<?php foot();
