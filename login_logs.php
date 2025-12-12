<?php
include 'includes/db.php';
include 'includes/functions.php';
require_admin();
include 'includes/header.php';

$res = $conn->query('SELECT l.id, u.username, l.ip_address, l.created_at FROM login_logs l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 200');

?>
<div class="card shadow-sm">
  <div class="card-body">
    <h3 class="card-title">Login Logs</h3>
    <table class="table table-sm table-bordered">
      <thead><tr><th>ID</th><th>User</th><th>IP Address</th><th>Time</th></tr></thead>
      <tbody>
        <?php while($row = $res->fetch_assoc()): ?>
          <tr>
            <td><?php echo esc($row['id']); ?></td>
            <td><?php echo esc($row['username']); ?></td>
            <td><?php echo esc($row['ip_address']); ?></td>
            <td><?php echo esc($row['created_at']); ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
  </div>
</div>
<?php include 'includes/footer.php'; ?>