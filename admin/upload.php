<?php
require '../db.php';
if(!is_admin()) { header('Location: login.php'); exit; }
if($_SERVER['REQUEST_METHOD']!=='POST' || empty($_FILES['csvfile'])){ header('Location: dashboard.php'); exit; }

$f = $_FILES['csvfile'];
if($f['error']!==UPLOAD_ERR_OK) { die('Upload error'); }
$contents = file_get_contents($f['tmp_name']);
$checksum = hash('sha256', $contents);

$st = $pdo->prepare('SELECT id FROM uploads WHERE checksum = :c LIMIT 1');
$st->execute([':c'=>$checksum]);
if($st->fetch()){ die('This file (or identical file) has already been uploaded. Duplicate processing skipped.'); }

$stmt = $pdo->prepare('INSERT INTO uploads (filename, checksum, uploaded_by) VALUES (:fn, :c, :by)');
$stmt->execute([':fn'=>$f['name'], ':c'=>$checksum, ':by'=>$_SESSION['admin_id']]);

$handle = fopen($f['tmp_name'], 'r');
$header = fgetcsv($handle);
$map = array_map('strtolower', $header ?: []);
while(($row = fgetcsv($handle)) !== false){
    $data = array_combine($map, $row);
    $title = trim($data['title'] ?? '');
    if($title==='') continue;
    $author = trim($data['author'] ?? '');
    $issn = trim($data['issn'] ?? '');
    $subject = trim($data['subject'] ?? '');
    $department = trim($data['department'] ?? '');
    $publisher = trim($data['publisher'] ?? '');
    $type = trim($data['type'] ?? '');
    $link = trim($data['link'] ?? '');

    try {
        $ins = $pdo->prepare('INSERT INTO records (title,author,issn,subject,department,publisher,type,link) VALUES (:title,:author,:issn,:subject,:department,:publisher,:type,:link)');
        $ins->execute([':title'=>$title,':author'=>$author,':issn'=>$issn,':subject'=>$subject,':department'=>$department,':publisher'=>$publisher,':type'=>$type,':link'=>$link]);
    } catch (PDOException $e){
        continue;
    }
}
fclose($handle);

header('Location: dashboard.php?uploaded=1'); exit;
?>