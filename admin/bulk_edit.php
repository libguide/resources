<?php
require '../db.php';
if(!is_admin()) { header('Location: login.php'); exit; }
$ids = $_GET['ids'] ?? '';
$ids_arr = array_filter(array_map('intval', explode(',', $ids)));
if(empty($ids_arr)) { echo 'No ids provided'; exit; }

if($_SERVER['REQUEST_METHOD']==='POST'){
    $fields = ['subject','department','publisher','type'];
    $sets = []; $params = [];
    foreach($fields as $f){
        if(isset($_POST[$f]) && $_POST[$f] !== ''){
            $sets[] = "$f = :$f";
            $params[":$f"] = $_POST[$f];
        }
    }
    if($sets){
        $in = implode(',', array_fill(0, count($ids_arr), '?'));
        $sql = 'UPDATE records SET '.implode(', ', $sets).' WHERE id IN ('.$in.')';
        $st = $pdo->prepare($sql);
        $st->execute(array_merge(array_values($params), $ids_arr));
    }
    header('Location: ../index.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Bulk Edit</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body>
<div class="container my-4">
  <div class="card p-3 mx-auto" style="max-width:600px;">
    <h4>Bulk Edit (<?=count($ids_arr)?> records)</h4>
    <form method="post">
      <p class="small">Provide new values for fields below. Leave blank to keep existing values.</p>
      <div class="mb-2"><label>Subject</label><input name="subject" class="form-control"></div>
      <div class="mb-2"><label>Department</label><input name="department" class="form-control"></div>
      <div class="mb-2"><label>Publisher</label><input name="publisher" class="form-control"></div>
      <div class="mb-2"><label>Type</label><input name="type" class="form-control"></div>
      <input type="hidden" name="ids" value="<?=htmlspecialchars($ids)?>">
      <div class="d-flex gap-2"><button class="btn btn-primary">Apply to selected</button><a class="btn btn-outline-secondary" href="../index.php">Cancel</a></div>
    </form>
  </div>
</div>
</body></html>
