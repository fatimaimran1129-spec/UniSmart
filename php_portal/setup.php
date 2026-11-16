<?php // Run once: http://localhost/UniSmart/php_portal/setup.php  (safe to run again after an update)
$p = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', ''); $p->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$p->exec('CREATE DATABASE IF NOT EXISTS unismart CHARACTER SET utf8mb4'); $p->exec('USE unismart');
foreach ([
 'CREATE TABLE IF NOT EXISTS users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(80),email VARCHAR(100) UNIQUE,pass VARCHAR(255),role ENUM("admin","teacher","student"))',
 'CREATE TABLE IF NOT EXISTS assignments(id INT AUTO_INCREMENT PRIMARY KEY,teacher_id INT,title VARCHAR(150),description TEXT,file VARCHAR(80) DEFAULT NULL,created TIMESTAMP DEFAULT CURRENT_TIMESTAMP)',
 'CREATE TABLE IF NOT EXISTS submissions(id INT AUTO_INCREMENT PRIMARY KEY,assignment_id INT,student_id INT,text LONGTEXT,file VARCHAR(80) DEFAULT NULL,plag_score INT DEFAULT NULL,plag_with VARCHAR(40) DEFAULT NULL,created TIMESTAMP DEFAULT CURRENT_TIMESTAMP)',
 'CREATE TABLE IF NOT EXISTS quizzes(id INT AUTO_INCREMENT PRIMARY KEY,teacher_id INT,title VARCHAR(150),data LONGTEXT)',
 'CREATE TABLE IF NOT EXISTS complaints(id INT AUTO_INCREMENT PRIMARY KEY,student_id INT,text TEXT,dept VARCHAR(30),created TIMESTAMP DEFAULT CURRENT_TIMESTAMP)',
 'CREATE TABLE IF NOT EXISTS feedback(id INT AUTO_INCREMENT PRIMARY KEY,student_id INT,teacher_id INT,text TEXT,sentiment VARCHAR(20),created TIMESTAMP DEFAULT CURRENT_TIMESTAMP)'] as $s) $p->exec($s);
// Upgrade tables created by the older version (adds the new "file" columns; errors = column already exists)
foreach (['ALTER TABLE assignments ADD COLUMN file VARCHAR(80) DEFAULT NULL', 'ALTER TABLE submissions ADD COLUMN file VARCHAR(80) DEFAULT NULL', 'ALTER TABLE submissions MODIFY text LONGTEXT'] as $s) { try { $p->exec($s); } catch (Exception $x) {} }
$i = $p->prepare('INSERT IGNORE INTO users(name,email,pass,role) VALUES(?,?,?,?)');
foreach ([['Admin','admin@uni.com','admin123','admin'],['Sir Ahmed','teacher@uni.com','teacher123','teacher'],['Ali Student','student@uni.com','student123','student']] as $u)
    $i->execute([$u[0], $u[1], password_hash($u[2], PASSWORD_DEFAULT), $u[3]]);
if (!is_dir(__DIR__ . '/uploads')) mkdir(__DIR__ . '/uploads', 0777, true);
echo 'Setup complete. Demo logins: admin@uni.com/admin123, teacher@uni.com/teacher123, student@uni.com/student123. <a href="login.php">Login</a>';
