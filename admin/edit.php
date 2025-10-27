<?php
require '../db.php';
if(!is_admin()) { header('Location: login.php'); exit; }
$id = $_GET['id'] ?? null;
if(!$id) { header('Location: ../index.php'); exit; }
if($_SERVER['REQUEST_METHOD']==='POST'){
    $fields = ['title','author','issn','subject','department','publisher','type','link'];
    $params = [];
    foreach($fields as $f) $params[":$f"] = $_POST[$f] ?? '';
    $params[':id'] = $id;
    $upd = $pdo->prepare('UPDATE records SET title=:title,author=:author,issn=:issn,subject=:subject,department=:department,publisher=:publisher,type=:type,link=:link WHERE id=:id');
    $upd->execute($params);
    header('Location: ../index.php'); exit;
}
$stmt = $pdo->prepare('SELECT * FROM records WHERE id = :id LIMIT 1');
$stmt->execute([':id'=>$id]);
$rec = $stmt->fetch();
if(!$rec) { echo 'Not found'; exit; }
?>
<!doctype html><html><head><meta charset="utf-8"><title>Edit</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
</head><body>
<div class="container my-4">
  <div class="card p-3 mx-auto" style="max-width:800px;">
    <h4>Edit record</h4>
    <form method="post">
      <?php foreach(['title','author','issn','subject','department','publisher','type','link'] as $f): ?>
        <div class="mb-2"><label class="form-label"><?=ucfirst($f)?></label>
        <?php if($f==='title' || $f==='link') echo "<textarea name=\"$f\" required class=\"form-control\">".htmlspecialchars($rec[$f])."</textarea>"; else echo "<input name=\"$f\" class=\"form-control\" value=\"".htmlspecialchars($rec[$f])."\">"; ?></div>
      <?php endforeach; ?>
      <div class="d-flex gap-2"><button class="btn btn-primary">Save</button><a class="btn btn-outline-secondary" href="../index.php">Cancel</a></div>
    </form>
  </div>
</div>
</body></html>
