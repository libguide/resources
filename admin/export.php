<?php
require '../db.php';
if(!is_admin()) { header('Location: login.php'); exit; }
if(isset($_GET['all']) && $_GET['all']=='1'){
    $stmt = $pdo->query('SELECT * FROM records');
} else if(!empty($_POST['export_ids'])){
    $ids = array_filter(array_map('intval', explode(',', $_POST['export_ids'])));
    if(!$ids){ header('Location: ../index.php'); exit; }
    $in = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM records WHERE id IN ($in)");
    $stmt->execute($ids);
} else {
    header('Location: ../index.php'); exit;
}
$rows = $stmt->fetchAll();
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="export.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['id','title','author','issn','subject','department','publisher','type','link','created_at']);
foreach($rows as $r) fputcsv($out, [$r['id'],$r['title'],$r['author'],$r['issn'],$r['subject'],$r['department'],$r['publisher'],$r['type'],$r['link'],$r['created_at']]);
fclose($out);
exit;
?>