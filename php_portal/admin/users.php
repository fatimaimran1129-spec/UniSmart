<?php require '../inc.php'; need('admin');
if ($_POST) q('INSERT IGNORE INTO users(name,email,pass,role) VALUES(?,?,?,"teacher")', [$_POST['name'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT)]);
head('Users'); ?>
<form method="post" class="card"><h3>Add a teacher</h3><input name="name" placeholder="Full name" required><input name="email" type="email" placeholder="Email" required><input name="password" placeholder="Password" required><button>Add Teacher</button></form>
<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>
<?php foreach (q('SELECT * FROM users ORDER BY id')->fetchAll(PDO::FETCH_ASSOC) as $u) echo '<tr><td>' . $u['id'] . '</td><td>' . e($u['name']) . '</td><td>' . e($u['email']) . '</td><td>' . $u['role'] . '</td></tr>'; ?></table>
<?php foot();
