<?php
session_start();
define('BASE', str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__)));
const AI_URL = 'http://127.0.0.1:8001';
const UPLOAD_DIR = __DIR__ . '/uploads/';
function db() { static $p; if (!$p) { $p = new PDO('mysql:host=localhost;dbname=unismart;charset=utf8mb4', 'root', ''); $p->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); } return $p; }
function q($sql, $a = []) { $s = db()->prepare($sql); $s->execute($a); return $s; }
function e($v) { return htmlspecialchars((string)$v); }
function ai($path, $data) {
    $c = curl_init(AI_URL . $path);
    curl_setopt_array($c, [CURLOPT_POST => 1, CURLOPT_HTTPHEADER => ['Content-Type: application/json'], CURLOPT_POSTFIELDS => json_encode($data), CURLOPT_RETURNTRANSFER => 1, CURLOPT_TIMEOUT => 20]);
    $r = curl_exec($c); curl_close($c);
    return $r ? json_decode($r, true) : null;   // null = AI service band hai
}
function need($role) { if (empty($_SESSION['u']) || $_SESSION['u']['role'] !== $role) { header('Location: ' . BASE . '/login.php'); exit; } }
function bars($rows) { $mx = max(array_merge([1], array_column($rows, 1))); foreach ($rows as $r) echo '<div class=bar><span>' . e($r[0]) . '</span><i style="width:' . (int)($r[1] / $mx * 100) . '%"></i><b>' . (int)$r[1] . '</b></div>'; }
function head($t) {
    $u = $_SESSION['u'] ?? null;
    $m = ['admin' => ['dashboard' => 'Dashboard', 'users' => 'Users', 'complaints' => 'Complaints', 'feedback' => 'Feedback'],
          'teacher' => ['dashboard' => 'Dashboard', 'assignments' => 'Assignments', 'quiz' => 'Quiz Generator'],
          'student' => ['dashboard' => 'Dashboard', 'assignments' => 'Assignments', 'quiz' => 'Quiz', 'complaint' => 'Complaint', 'feedback' => 'Feedback', 'chatbot' => 'Chatbot']];
    echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . e($t) . ' | UniSmart</title><style>
body{font-family:system-ui,sans-serif;background:#f3f6fb;margin:0}nav{background:#1f4e8c;color:#fff;padding:.7rem 1rem;display:flex;gap:1rem;flex-wrap:wrap;align-items:center}
nav a{color:#fff;text-decoration:none}nav span{margin-left:auto}main{max-width:900px;margin:1rem auto;padding:0 1rem}
.card{background:#fff;border-radius:10px;padding:1rem;margin:.8rem 0;box-shadow:0 1px 4px #0002}input,select,textarea{display:block;width:100%;padding:.6rem;margin:.4rem 0;box-sizing:border-box;border:1px solid #bbb;border-radius:6px}
button{padding:.6rem 1.2rem;background:#1f4e8c;color:#fff;border:0;border-radius:6px;cursor:pointer}table{width:100%;border-collapse:collapse;background:#fff}td,th{border:1px solid #ddd;padding:8px;text-align:left}
.err{color:#b00}.ok{color:#080}.stat{display:inline-block;background:#fff;padding:1rem 1.5rem;margin:.3rem;border-radius:10px;box-shadow:0 1px 4px #0002}.stat b{display:block;font-size:1.8rem;color:#1f4e8c}
.bar{display:flex;align-items:center;gap:8px;margin:4px 0}.bar span{width:110px}.bar i{height:16px;background:#1f4e8c;border-radius:4px;display:block}
</style></head><body><nav><b>UniSmart</b>';
    if ($u) { foreach ($m[$u['role']] as $f => $l) echo '<a href="' . BASE . '/' . $u['role'] . '/' . $f . '.php">' . $l . '</a>';
        echo '<span>' . e($u['name']) . ' (' . $u['role'] . ') <a href="' . BASE . '/logout.php">Logout</a></span>'; }
    echo '</nav><main><h2>' . e($t) . '</h2>';
}
function foot() { echo '</main></body></html>'; }
function ai_down() { return '<p class=err>The AI service is not running. Start it first with <code>uvicorn app:app --port 8001</code>.</p>'; }
// Upload helpers: save_upload() -> stored file name, null (no file chosen) or false (invalid file)
function save_upload($f) {
    if (empty($_FILES[$f]['name']) || $_FILES[$f]['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($_FILES[$f]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['pdf', 'docx', 'txt']) || $_FILES[$f]['size'] > 5 * 1024 * 1024) return false;
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    move_uploaded_file($_FILES[$f]['tmp_name'], UPLOAD_DIR . $name);
    return $name;
}
function file_text($name) {   // text inside an uploaded file (needed for plagiarism check and quiz)
    $path = UPLOAD_DIR . $name;
    if (pathinfo($name, PATHINFO_EXTENSION) === 'txt') return file_get_contents($path);
    $r = ai('/extract', ['filename' => $name, 'data' => base64_encode(file_get_contents($path))]);
    return $r['text'] ?? null;
}
function file_link($name) { return $name ? '<a href="' . BASE . '/uploads/' . e($name) . '" target="_blank">Download file</a>' : ''; }
