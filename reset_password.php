<?php
include 'includes/db.php';
include 'includes/header.php';
include 'includes/functions.php';

$token = $_GET['token'] ?? '';
$msg = '';

if (!$token) {
    echo '<div class="alert alert-danger">Invalid token.</div>';
    include 'includes/footer.php';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (strlen($password) < 6) {
        $msg = 'Password must be at least 6 characters.';
    } else {
        $stmt = $conn->prepare('SELECT user_id, expires_at FROM password_resets WHERE token = ? LIMIT 1');
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $r = $stmt->get_result();
        if ($row = $r->fetch_assoc()) {
            if (strtotime($row['expires_at']) < time()) {
                $msg = 'Token expired.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $up = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
                $up->bind_param('si', $hash, $row['user_id']);
                if ($up->execute()) {
                    // delete token
                    $del = $conn->prepare('DELETE FROM password_resets WHERE token = ?');
                    $del->bind_param('s', $token);
                    $del->execute();
                    echo '<div class="alert alert-success">Password updated. <a href="index.php">Login</a></div>';
                    include 'includes/footer.php';
                    exit();
                } else {
                    $msg = 'Failed to update password.';
                }
            }
        } else {
            $msg = 'Invalid token.';
        }
    }
}

?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4>Reset Password</h4>
        <?php if($msg): ?><div class="alert alert-danger"><?php echo esc($msg); ?></div><?php endif; ?>
        <form method="POST">
          <div class="mb-3"><label class="form-label">New Password</label><input class="form-control" type="password" name="password" required></div>
          <button class="btn btn-primary">Set New Password</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>