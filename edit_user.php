<?php
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/audit.php'; // 🔹 add audit logging
require_admin();
include 'includes/header.php';

$user_id = intval($_GET['id'] ?? 0);
if (!$user_id) { 
  echo '<div class="alert alert-danger">Invalid user.</div>'; 
  include 'includes/footer.php'; 
  exit(); 
}

// fetch user
$stmt = $conn->prepare('SELECT id, username, email, role_id FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) { 
  echo '<div class="alert alert-danger">User not found.</div>'; 
  include 'includes/footer.php'; 
  exit(); 
}

// fetch roles
$roles = $conn->query('SELECT id, role_name FROM roles');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_role = intval($_POST['role_id'] ?? 0);
    if ($new_role <= 0) $errors[] = 'Select a valid role.';

    if (empty($errors)) {
        $up = $conn->prepare('UPDATE users SET role_id = ? WHERE id = ?');
        $up->bind_param('ii', $new_role, $user_id);

        if ($up->execute()) {
            echo '<div class="alert alert-success">Role updated successfully.</div>';

            // reload updated user data
            $stmt->execute(); 
            $user = $stmt->get_result()->fetch_assoc();

            // get role name for logs
            $role_stmt = $conn->prepare('SELECT role_name FROM roles WHERE id = ?');
            $role_stmt->bind_param('i', $new_role);
            $role_stmt->execute();
            $role_name = $role_stmt->get_result()->fetch_assoc()['role_name'] ?? 'Unknown';
            $role_stmt->close();

            // 🔹 Log the role update
            $admin_id = $_SESSION['user_id'];
            $admin_name = $_SESSION['username'];
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            log_action($conn, $admin_id, 'Role updated', 
                "Admin: $admin_name changed role of user ID $user_id ({$user['username']}) to '$role_name' from IP: $ip"
            );

        } else {
            $errors[] = 'Update failed: ' . $up->error;
            log_action($conn, $_SESSION['user_id'], 'Role update failed', 'Error: ' . $up->error);
        }
    }
}
?>
<div class="card shadow-sm">
  <div class="card-body">
    <h3 class="card-title"><i class="bi bi-person-gear me-2"></i>Edit User - <?php echo esc($user['username']); ?></h3>
    <?php if(!empty($errors)) echo '<div class="alert alert-danger"><ul><li>'.implode('</li><li>', array_map('esc', $errors)).'</li></ul></div>'; ?>
    <form method="POST" action="edit_user.php?id=<?php echo $user_id; ?>">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" value="<?php echo esc($user['email']); ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role_id" class="form-select" required>
          <?php while($r = $roles->fetch_assoc()): ?>
            <option value="<?php echo $r['id']; ?>" <?php if($r['id']==$user['role_id']) echo 'selected'; ?>>
              <?php echo esc($r['role_name']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Role</button>
    </form>
    <p class="mt-3"><a href="manage_users.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a></p>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
