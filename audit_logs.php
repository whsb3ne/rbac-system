<?php
include 'includes/db.php';
include 'includes/functions.php';
require_admin();
include 'includes/header.php';
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
  .search-section {
    margin-bottom: 2rem;
  }
  .search-section input,
  .search-section button {
    border-radius: 12px;
    border: 2px solid #e0e0e0;
    padding: 0.75rem;
  }
  .search-section input:focus {
    border-color: #0f1e3d;
    box-shadow: 0 0 0 3px rgba(15, 30, 61, 0.1);
    outline: none;
  }
  .search-btn {
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);
    color: white;
    border: none;
    font-weight: 600;
  }
  .search-btn:hover {
    transform: scale(1.02);
    box-shadow: 0 5px 20px rgba(15, 30, 61, 0.3);
    color: white;
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
</style>

<div class="page-header">
  <h1><i class="bi bi-clipboard-data me-2"></i>Audit Logs</h1>
</div>

<div class="card-modern">
  <div class="card-body">
    <form class="search-section" method="GET">
      <div class="row">
        <div class="col-md-9">
          <input type="text" name="search" class="form-control" placeholder="Search by action, user, or details..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>
        <div class="col-md-3">
          <button class="btn search-btn w-100">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
      </div>
    </form>

    <?php
    $search = trim($_GET['search'] ?? '');
    $query = "
      SELECT a.id, a.user_id, a.action, a.details, a.timestamp, u.username
      FROM audit_logs a
      LEFT JOIN users u ON a.user_id = u.id
    ";
    if ($search !== '') {
        $query .= " WHERE a.action LIKE ? OR a.details LIKE ? OR u.username LIKE ?";
        $stmt = $conn->prepare($query . " ORDER BY a.timestamp DESC LIMIT 100");
        $param = "%$search%";
        $stmt->bind_param('sss', $param, $param, $param);
    } else {
        $stmt = $conn->prepare($query . " ORDER BY a.timestamp DESC LIMIT 100");
    }

    $stmt->execute();
    $logs = $stmt->get_result();

    if ($logs->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle table-modern">
          <thead>
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Action</th>
              <th>Details</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody>
          <?php while ($row = $logs->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['username'] ?? 'System/Unknown') ?></td>
              <td><span class="badge" style="background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);"><?= htmlspecialchars($row['action']) ?></span></td>
              <td><?= htmlspecialchars($row['details']) ?></td>
              <td><?= htmlspecialchars($row['timestamp']) ?></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-info" style="border-radius: 12px; background: #e7f3ff; border: 2px solid #0f1e3d; color: #0f1e3d;">No logs found.</div>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
