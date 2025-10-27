<?php
// admin/logout.php
// Robust logout: clear session, delete cookie, redirect to main page.

ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = [];

// Delete the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'] ?? '/',
        $params['domain'] ?? '',
        $params['secure'] ?? false,
        $params['httponly'] ?? true
    );
}

// Destroy the session completely
session_unset();
session_destroy();

// Redirect to main page
$redirect = '../index.php';
if (!headers_sent()) {
    header('Location: ' . $redirect);
    exit;
}

// Fallback if headers already sent
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Logged out</title>
  <meta http-equiv="refresh" content="0;url=<?= htmlspecialchars($redirect) ?>">
  <script>window.location.href = <?= json_encode($redirect) ?>;</script>
</head>
<body>
  <p>Logged out. If you are not redirected automatically, 
  <a href="<?= htmlspecialchars($redirect) ?>">click here</a>.</p>
</body>
</html>
<?php
ob_end_flush();
?>
