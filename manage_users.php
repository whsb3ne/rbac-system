<?php
include 'includes/db.php';
include 'includes/functions.php';
require_admin();
include 'includes/header.php';

$query = 'SELECT users.id, users.username, users.email, roles.role_name FROM users JOIN roles ON users.role_id = roles.id ORDER BY users.id DESC';
$res = $conn->query($query);
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
  .btn-edit {
    background: #0f1e3d;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  .btn-edit:hover {
    background: #1a2f5a;
    color: white;
    transform: scale(1.05);
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
  .back-link {
    color: white;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  .back-link:hover {
    color: #e0e0e0;
    text-decoration: underline;
  }
</style>

<div class="page-header">
  <h1><i class="bi bi-people-fill me-2"></i>Manage Users</h1>
</div>

<div class="card-modern">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle table-modern">
        <thead>
          <tr>
            <th>#</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $res->fetch_assoc()): ?>
            <tr>
              <td><?php echo esc($row['id']); ?></td>
              <td><?php echo esc($row['username']); ?></td>
              <td><?php echo esc($row['email']); ?></td>
              <td><span class="badge" style="background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);"><?php echo esc($row['role_name']); ?></span></td>
              <td>
                <a class="btn-edit" href="edit_user.php?id=<?php echo $row['id']; ?>"><i class="bi bi-pencil me-1"></i>Edit</a>
                <?php if($row['id'] != $_SESSION['user_id']): ?>
                  <a class="btn-delete" href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this user?')"><i class="bi bi-trash me-1"></i>Delete</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <p class="mt-3"><a class="back-link" href="dashboard.php"><i class="bi bi-arrow-left me-1"></i>Back to Dashboard</a></p>
  </div>
</div>
<?php include 'includes/footer.php'; ?>