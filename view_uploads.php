<?php
include 'includes/db.php';
include 'includes/functions.php';
require_login();
include 'includes/header.php';

$role = $_SESSION['role'];
if ($role === 'Admin' || $role === 'Editor') {
    $query = "SELECT u.id, u.filename, u.filepath, u.role_visibility, us.username, u.created_at 
              FROM uploads u JOIN users us ON u.uploaded_by = us.id 
              ORDER BY u.created_at DESC";
    $res = $conn->query($query);
} else {
    $query = "SELECT u.id, u.filename, u.filepath, u.role_visibility, us.username, u.created_at 
              FROM uploads u JOIN users us ON u.uploaded_by = us.id 
              WHERE u.role_visibility IN ('All','Viewer')
              ORDER BY u.created_at DESC";
    $res = $conn->query($query);
}
?>

<style>
  main {
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%) !important;
    min-height: 100vh;
  }
  .page-header {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  }
  .page-header h1 {
    color: #0f1e3d;
    font-weight: 700;
    margin: 0;
  }
  .card-modern {
    background: white;
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
  }
  .card-modern .card-body {
    padding: 2rem;
  }
  .table-modern {
    border: none;
  }
  .table-modern thead {
    background: #f8f9fa;
    border-bottom: 2px solid #0f1e3d;
  }
  .table-modern thead th {
    color: #0f1e3d;
    font-weight: 700;
    border: none;
  }
  .table-modern tbody tr {
    border-bottom: 1px solid #e0e0e0;
    transition: all 0.3s ease;
  }
  .table-modern tbody tr:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
  }
  .btn-download {
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  .btn-download:hover {
    color: white;
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(15, 30, 61, 0.3);
  }
  .btn-delete {
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  .btn-delete:hover {
    background: #c82333;
    color: white;
    transform: scale(1.05);
  }
</style>

<div class="page-header">
  <h1><i class="bi bi-cloud-check-fill me-2"></i>Shared Files</h1>
</div>

<div class="card-modern">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle table-modern">
        <thead>
          <tr>
            <th>Filename</th>
            <th>Uploaded By</th>
            <th>Visible To</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $res->fetch_assoc()): ?>
            <tr>
              <td><?= esc($row['filename']) ?></td>
              <td><?= esc($row['username']) ?></td>
              <td><span class="badge" style="background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);"><?= esc($row['role_visibility']) ?></span></td>
              <td><?= esc($row['created_at']) ?></td>
              <td>
                <a class="btn-download" href="<?= esc($row['filepath']) ?>" download><i class="bi bi-download me-1"></i>Download</a>
                <?php if ($role === 'Admin' || $role === 'Editor'): ?>
                  <a class="btn-delete" href="delete_upload.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this file?')"><i class="bi bi-trash me-1"></i>Delete</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
