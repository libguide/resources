<?php
require 'db.php';

$perPage = 15;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page-1)*$perPage;

$subject = $_GET['subject'] ?? '';
$department = $_GET['department'] ?? '';
$publisher = $_GET['publisher'] ?? '';
$search = $_GET['q'] ?? '';

$where = [];
$params = [];
if ($subject !== '') { $where[] = 'subject = :subject'; $params[':subject'] = $subject; }
if ($department !== '') { $where[] = 'department = :department'; $params[':department'] = $department; }
if ($publisher !== '') { $where[] = 'publisher = :publisher'; $params[':publisher'] = $publisher; }
if ($search !== '') { $where[] = '(title LIKE :q OR author LIKE :q OR issn LIKE :q)'; $params[':q'] = "%$search%"; }

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT COUNT(*) FROM records $where_sql");
$stmt->execute($params);
$total = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM records $where_sql ORDER BY created_at DESC LIMIT :lim OFFSET :off");
foreach($params as $k=>$v) $stmt->bindValue($k, $v);
$stmt->bindValue(':lim', (int)$perPage, PDO::PARAM_INT);
$stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();

$subjects = $pdo->query('SELECT DISTINCT subject FROM records WHERE subject IS NOT NULL AND subject != "" LIMIT 200')->fetchAll(PDO::FETCH_COLUMN);
$departments = $pdo->query('SELECT DISTINCT department FROM records WHERE department IS NOT NULL AND department != "" LIMIT 200')->fetchAll(PDO::FETCH_COLUMN);
$publishers = $pdo->query('SELECT DISTINCT publisher FROM records WHERE publisher IS NOT NULL AND publisher != "" LIMIT 200')->fetchAll(PDO::FETCH_COLUMN);

function qp($params) {
    $q = $_GET;
    foreach($params as $k=>$v) $q[$k]=$v;
    return http_build_query($q);
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>E-Resource Management Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">E-Resource Management Portal</h1>
    <div>
      <?php if(is_admin()): ?>
        <a class="btn btn-outline-secondary btn-sm" href="admin/dashboard.php">Admin</a>
        <a class="btn btn-outline-danger btn-sm" href="admin/logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-outline-primary btn-sm" href="admin/login.php">Admin Login</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="card p-3 mb-3">
    <form method="get" class="row g-2 align-items-center">
      <div class="col-md-3">
        <select name="subject" class="form-select">
          <option value="">All subjects</option>
          <?php foreach($subjects as $s): ?>
            <option value="<?= htmlspecialchars($s) ?>" <?= ($s === $subject) ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <select name="department" class="form-select">
          <option value="">All departments</option>
          <?php foreach($departments as $d): ?>
            <option value="<?= htmlspecialchars($d) ?>" <?= ($d === $department) ? 'selected' : '' ?>><?= htmlspecialchars($d) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <select name="publisher" class="form-select">
          <option value="">All publishers</option>
          <?php foreach($publishers as $p): ?>
            <option value="<?= htmlspecialchars($p) ?>" <?= ($p === $publisher) ? 'selected' : '' ?>><?= htmlspecialchars($p) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <input class="form-control" type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search title/author/issn">
      </div>
      <div class="col-md-1 d-grid">
        <button class="btn btn-primary">Filter</button>
      </div>
    </form>
  </div>

  <div class="card p-3 mb-3">
    <div class="d-flex justify-content-between mb-2">
      <div><strong><?= number_format($total) ?></strong> records</div>
      <div>
        <?php if(is_admin()): ?>
          <button class="btn btn-sm btn-outline-secondary" id="bulkEditBtn">Bulk Edit</button>
          <button class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn">Bulk Delete</button>
        <?php endif; ?>
        <button class="btn btn-sm btn-outline-success" id="exportBtn">Export Selected/Displayed</button>
      </div>
    </div>

    <form id="recordsForm" method="post" action="admin/export.php">
      <input type="hidden" name="export_ids" id="export_ids">
      <div class="table-responsive">
        <table class="table table-hover table-striped">
          <thead>
            <tr>
              <th style="width:40px;"><input id="chkAll" type="checkbox"></th>
              <th>Title</th>
              <th>Author</th>
              <th>ISSN</th>
              <th>Subject</th>
              <th>Publisher</th>
              <th>Type</th>
              <th>Link</th>
              <?php if(is_admin()) echo '<th>Actions</th>'; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach($rows as $r): ?>
            <tr>
              <td><input class="rowChk" type="checkbox" value="<?= $r['id'] ?>"></td>
              <td><?= htmlspecialchars($r['title']) ?></td>
              <td><?= htmlspecialchars($r['author']) ?></td>
              <td><?= htmlspecialchars($r['issn']) ?></td>
              <td><?= htmlspecialchars($r['subject']) ?></td>
              <td><?= htmlspecialchars($r['publisher']) ?></td>
              <td><?= htmlspecialchars($r['type']) ?></td>
              <td><?php if($r['link']) echo '<a href="'.htmlspecialchars($r['link']).'" target="_blank">Open</a>'; ?></td>
              <?php if(is_admin()): ?>
                <td>
                  <a href="admin/edit.php?id=<?= $r['id'] ?>" class="link-primary">Edit</a> |
                  <a href="admin/delete.php?id=<?= $r['id'] ?>" class="link-danger" onclick="return confirm('Delete this record?')">Delete</a>
                </td>
              <?php endif; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </form>

    <?php
    $pages = ceil($total / $perPage);
    if ($pages > 1):
    ?>
    <nav>
      <ul class="pagination">
        <?php for ($i=1; $i<=$pages; $i++): ?>
          <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
            <a class="page-link" href="?<?= qp(['page'=>$i]) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
    <?php endif; ?>
  </div>

  <div class="text-muted small-muted">
    Tip: Admins can upload CSVs (title,author,issn,subject,department,publisher,type,link). Duplicate files or duplicate records are skipped automatically.
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('chkAll').addEventListener('change', function(){
  document.querySelectorAll('.rowChk').forEach(cb => cb.checked = this.checked);
});

document.getElementById('exportBtn').addEventListener('click', function(){
  const checked = [...document.querySelectorAll('.rowChk')].filter(cb => cb.checked).map(cb => cb.value);
  const exportField = document.getElementById('export_ids');
  if(checked.length === 0){
    if(!confirm('No rows selected. Export ALL displayed rows?')) return;
    const all = [...document.querySelectorAll('.rowChk')].map(cb => cb.value);
    exportField.value = all.join(',');
  } else {
    exportField.value = checked.join(',');
  }
  document.getElementById('recordsForm').submit();
});

<?php if(is_admin()): ?>
document.getElementById('bulkDeleteBtn').addEventListener('click', function(){
  const ids = [...document.querySelectorAll('.rowChk')].filter(cb => cb.checked).map(cb => cb.value);
  if(ids.length === 0){ alert('Please select rows first'); return; }
  if(!confirm('Delete selected records? This cannot be undone')) return;
  const form = document.createElement('form');
  form.method = 'post';
  form.action = 'admin/delete.php';
  const inp = document.createElement('input');
  inp.type = 'hidden';
  inp.name = 'bulk_ids';
  inp.value = ids.join(',');
  form.appendChild(inp);
  document.body.appendChild(form);
  form.submit();
});

document.getElementById('bulkEditBtn').addEventListener('click', function(){
  const ids = [...document.querySelectorAll('.rowChk')].filter(cb => cb.checked).map(cb => cb.value);
  if(ids.length === 0){ alert('Please select rows to edit'); return; }
  const w = window.open('admin/bulk_edit.php?ids=' + ids.join(','), '_blank');
  if(!w) alert('Popup blocked — allow popups for this site to use bulk edit');
});
<?php endif; ?>
</script>
</body>
</html>
