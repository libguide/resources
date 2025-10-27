<?php
require '../db.php';
if(!is_admin()) { header('Location: login.php'); exit; }
?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/style.css">
</head><body>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4">Admin Dashboard</h2>
    <div><a class="btn btn-outline-secondary btn-sm" href="logout.php">Logout</a> <a class="btn btn-outline-primary btn-sm" href="../index.php">View Portal</a></div>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card p-3">
        <h5>Upload CSV</h5>
        <p class="small-muted">CSV columns: title,author,issn,subject,department,publisher,type,link</p>
        <form method="post" action="upload.php" enctype="multipart/form-data">
          <input type="file" name="csvfile" accept=".csv" class="form-control" required>
          <div class="mt-2"><button class="btn btn-primary">Upload and Process</button></div>
        </form>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-3">
        <h5>Data Management</h5>
        <div class="d-flex gap-2">
          <a class="btn btn-danger" href="delete.php?all=1" onclick="return confirm('Delete ALL records? This cannot be undone')">Delete all records</a>
          <a class="btn btn-success" href="export.php?all=1">Export all records</a>
        </div>
      </div>
    </div>
  </div>
</div>
</body></html>
