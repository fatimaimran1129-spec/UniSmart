<?php require 'inc.php';
if ($_POST) {
    $r = q('SELECT * FROM users WHERE email=?', [$_POST['email']])->fetch(PDO::FETCH_ASSOC);
    if ($r && password_verify($_POST['password'], $r['pass'])) { $_SESSION['u'] = $r; header('Location: ' . BASE . '/' . $r['role'] . '/dashboard.php'); exit; }
    $err = 'Incorrect email or password.';
}
head('Login'); if (!empty($err)) echo '<p class=err>' . $err . '</p>'; ?>
<form method="post" class="card"><input name="email" type="email" placeholder="Email" required><input name="password" type="password" placeholder="Password" required>
<button>Login</button><p><a href="register.php">Create a new student account</a></p></form>
<?php foot();
