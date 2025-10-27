<?php
// reset_admin_password.php
// Put this file in your site root, open in browser, reset the admin password,
// then DELETE this file immediately for security.

error_reporting(E_ALL);
ini_set('display_errors', 1);

$DB_HOST = '127.0.0.1';
$DB_NAME = 'library_portal';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    die('DB connection failed: ' . htmlspecialchars($e->getMessage()));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $msg = 'Provide username and password.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        // try update: if username exists update; else insert
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = :u LIMIT 1");
        $stmt->execute([':u'=>$username]);
        $row = $stmt->fetch();
        if ($row) {
            $pdo->prepare("UPDATE admins SET password_hash = :h WHERE id = :id")->execute([':h'=>$hash, ':id'=>$row['id']]);
            $msg = "Password for user '".htmlspecialchars($username)."' updated successfully.";
        } else {
            $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (:u, :h)")->execute([':u'=>$username, ':h'=>$hash]);
            $msg = "User '".htmlspecialchars($username)."' created with the new password.";
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Reset Admin Password</title></head>
<body style="font-family:system-ui,Arial;margin:30px">
  <h2>Reset / Create Admin</h2>
  <?php if(!empty($msg)): ?><div style="padding:10px;background:#f3f4f6;border:1px solid #ddd;margin-bottom:10px;"><?=htmlspecialchars($msg)?></div><?php endif; ?>
  <form method="post">
    <div style="margin-bottom:8px"><label>Username<br><input name="username" style="width:320px;padding:8px" value="admin"></label></div>
    <div style="margin-bottom:8px"><label>New Password<br><input name="password" type="text" style="width:320px;padding:8px" value="ChangeMe123!"></label></div>
    <div><button style="padding:8px 14px">Set Password</button></div>
  </form>

  <p style="color:#a00"><strong>Security:</strong> After you log in, delete this file immediately.</p>
</body>
</html>
