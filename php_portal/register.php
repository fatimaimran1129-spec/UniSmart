<?php require 'inc.php';
if ($_POST) {
    try { q('INSERT INTO users(name,email,pass,role) VALUES(?,?,?,"student")', [$_POST['name'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT)]); header('Location: login.php'); exit; }
    catch (Exception $x) { $err = 'This email is already registered.'; }
}
head('Student Registration'); if (!empty($err)) echo '<p class=err>' . $err . '</p>'; ?>
<form method="post" class="card"><input name="name" placeholder="Full name" required><input name="email" type="email" placeholder="Email" required><input name="password" type="password" placeholder="Password" required><button>Register</button></form>
<?php foot();
