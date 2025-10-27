<?php
require '../db.php';
if(!is_admin()) { header('Location: login.php'); exit; }
if(isset($_GET['all']) && $_GET['all']=='1'){
    $pdo->exec('TRUNCATE TABLE records');
    header('Location: dashboard.php?deleted_all=1'); exit;
}
if($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['bulk_ids'])){
    $ids = array_filter(array_map('intval', explode(',', $_POST['bulk_ids'])));
    if($ids){
        $in = implode(',', array_fill(0, count($ids), '?'));
        $st = $pdo->prepare("DELETE FROM records WHERE id IN ($in)");
        $st->execute($ids);
    }
    header('Location: ../index.php'); exit;
}
$id = $_GET['id'] ?? null;
if($id){
    $st = $pdo->prepare('DELETE FROM records WHERE id = :id');
    $st->execute([':id'=>$id]);
}
header('Location: ../index.php'); exit;
?>