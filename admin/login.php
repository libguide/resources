<?php
require '../db.php';
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = :u LIMIT 1');
    $stmt->execute([':u'=>$u]);
    $admin = $stmt->fetch();
    if($admin && password_verify($p, $admin['password_hash'])){
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: dashboard.php'); exit;
    } else $err='Invalid credentials';
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
</head><body>
<div class="container d-flex align-items-center" style="min-height:70vh;">
  <div class="card p-4 mx-auto" style="width:100%;max-width:420px;">
    <h4 class="mb-3">Admin Login</h4>
    <form method="post">
      <div class="mb-2"><input name="username" class="form-control" placeholder="Username" required></div>
      <div class="mb-3"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
      <div class="d-flex gap-2"><button class="btn btn-primary">Login</button><a class="btn btn-outline-secondary" href="../index.php">Back</a></div>
      <?php if($err): ?><div class="text-danger mt-2"><?=htmlspecialchars($err)?></div><?php endif; ?>
    </form>
  </div>
</div>
</body></html>
