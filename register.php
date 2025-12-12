<?php
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/audit.php'; // 🔹 Include audit logging helper
include 'includes/header.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username) $errors[] = 'Username required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';

    if (empty($errors)) {
        // check if email exists
        $check = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->bind_param('s', $email);
        $check->execute();
        $r = $check->get_result();

        if ($r->fetch_assoc()) {
            $errors[] = 'Email already registered.';
            log_action($conn, null, 'Failed registration', "Duplicate email attempt: $email");
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role_id = 3; // default role: viewer
            $ins = $conn->prepare('INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)');
            $ins->bind_param('sssi', $username, $email, $hash, $role_id);

            if ($ins->execute()) {
                echo '<div class="alert alert-success">Registration successful. <a href="index.php">Login now</a></div>';

                // 🔹 Log successful registration
                $new_user_id = $conn->insert_id;
                log_action($conn, $new_user_id, 'User registered', "New account created for $email");
            } else {
                $errors[] = 'Registration failed: ' . $ins->error;
                log_action($conn, null, 'Failed registration', 'Database insert error: ' . $ins->error);
            }
        }
    }
}
?>
<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card shadow-sm">
      <div class="card-body">
        <h3 class="card-title mb-3">Create an account</h3>
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger"><ul><?php foreach($errors as $e) echo '<li>'.esc($e).'</li>'; ?></ul></div>
        <?php endif; ?>
        <form method="POST" action="register.php" autocomplete="off">
          <div class="mb-3">
            <label class="form-label">Full name</label>
            <input class="form-control" name="username" required value="<?php echo esc($_POST['username'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" required value="<?php echo esc($_POST['email'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required>
          </div>
          <button class="btn btn-success">Register</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
