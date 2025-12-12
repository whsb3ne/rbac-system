<?php
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/audit.php';
require_login();
include 'includes/header.php';

$user_id = $_SESSION['user_id'];
$msg = '';
$errors = [];

$stmt = $conn->prepare("SELECT username, email, role_id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username'] ?? '');
    $new_password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!$new_username) $errors[] = 'Username cannot be empty.';

    if ($new_password !== '') {
        if (strlen($new_password) < 6) $errors[] = 'Password must be at least 6 characters.';
        if ($new_password !== $confirm) $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        if ($new_password !== '') {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username=?, password=? WHERE id=?");
            $stmt->bind_param("ssi", $new_username, $hash, $user_id);
            $action_msg = "Updated username and password";
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=? WHERE id=?");
            $stmt->bind_param("si", $new_username, $user_id);
            $action_msg = "Updated username only";
        }

        if ($stmt->execute()) {
            $_SESSION['username'] = $new_username;
            $msg = '✅ Profile updated successfully.';
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            log_action($conn, $user_id, 'Profile updated', "$action_msg from IP: $ip");
        } else {
            $errors[] = 'Update failed: ' . $stmt->error;
            log_action($conn, $user_id, 'Profile update failed', 'DB error: ' . $stmt->error);
        }
        $stmt->close();
    }
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
    max-width: 600px;
    margin: 0 auto 2rem;
  }
  .card-modern .card-body {
    padding: 2rem;
  }
  .form-label {
    color: #0f1e3d;
    font-weight: 600;
    margin-bottom: 0.5rem;
  }
  .form-control {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 0.75rem;
    transition: all 0.3s ease;
  }
  .form-control:focus {
    border-color: #0f1e3d;
    box-shadow: 0 0 0 3px rgba(15, 30, 61, 0.1);
    outline: none;
  }
  .form-control:disabled,
  .form-control[readonly] {
    background-color: #f8f9fa;
    color: #6c757d;
  }
  .btn-update {
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);
    border: none;
    color: white;
    font-weight: 600;
    width: 100%;
    padding: 0.75rem;
    border-radius: 12px;
    margin-top: 1rem;
    transition: all 0.3s ease;
  }
  .btn-update:hover {
    transform: scale(1.02);
    box-shadow: 0 10px 30px rgba(15, 30, 61, 0.3);
    color: white;
  }
  .alert-success {
    border-radius: 12px;
    background: #e7f3e7;
    border: 2px solid #0f1e3d;
    color: #0f1e3d;
  }
  .alert-danger {
    border-radius: 12px;
    background: #fce7e7;
    border: 2px solid #dc3545;
    color: #dc3545;
  }
</style>

<div class="page-header">
  <h1><i class="bi bi-person-circle me-2"></i>Your Profile</h1>
</div>

<div class="card-modern">
  <div class="card-body">
    <?php if($msg): ?>
      <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if(!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="profile.php">
      <div class="mb-3">
        <label class="form-label"><i class="bi bi-envelope me-1"></i>Email (readonly)</label>
        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
      </div>
      <div class="mb-3">
        <label class="form-label"><i class="bi bi-person me-1"></i>Username</label>
        <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($user['username']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label"><i class="bi bi-lock me-1"></i>New Password (optional)</label>
        <input type="password" name="password" class="form-control" placeholder="Enter new password">
      </div>
      <div class="mb-3">
        <label class="form-label"><i class="bi bi-lock-fill me-1"></i>Confirm Password</label>
        <input type="password" name="confirm" class="form-control" placeholder="Confirm new password">
      </div>
      <div class="mb-3">
        <label class="form-label"><i class="bi bi-shield-check me-1"></i>Role</label>
        <?php
          $role = $conn->query("SELECT role_name FROM roles WHERE id=".(int)$user['role_id'])->fetch_assoc()['role_name'] ?? 'Unknown';
        ?>
        <input type="text" class="form-control" value="<?= htmlspecialchars($role) ?>" readonly>
      </div>
      <button class="btn btn-update"><i class="bi bi-save me-1"></i> Update Profile</button>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
