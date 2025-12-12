<?php
include 'includes/db.php';
include 'includes/header.php';
include 'includes/functions.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Provide a valid email.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $token = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
            $ins = $conn->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
            $ins->bind_param('iss', $row['id'], $token, $expires);
            $ins->execute();
            // In production: email the link. For demo, show the link.
            $reset_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/reset_password.php?token=' . $token;
            $msg = 'A reset link has been generated (for demo): <a href="' . $reset_link . '">Reset Password</a>';
        } else {
            $msg = 'Email not found.';
        }
    }
}

?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4>Forgot Password</h4>
        <?php if($msg): ?>
          <div class="alert alert-info"><?php echo $msg; ?></div>
        <?php endif; ?>
        <form method="POST">
          <div class="mb-3"><label class="form-label">Email</label><input class="form-control" name="email" type="email" required></div>
          <button class="btn btn-primary">Generate Reset Link</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>