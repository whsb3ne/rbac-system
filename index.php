<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/audit.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $err = 'Please provide both email and password.';
        log_action($conn, null, 'Failed login', 'Missing email or password');
    } else {
        $stmt = $conn->prepare('SELECT users.id, users.username, users.password, roles.role_name, users.role_id FROM users JOIN roles ON users.role_id = roles.id WHERE users.email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role_name'];
                $_SESSION['role_id'] = $row['role_id'];

                $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                $ins = $conn->prepare('INSERT INTO login_logs (user_id, ip_address) VALUES (?, ?)');
                $ins->bind_param('is', $row['id'], $ip);
                $ins->execute();
                $ins->close();

                log_action($conn, $row['id'], 'User logged in', 'Successful login from IP: ' . $ip);

                header('Location: dashboard.php');
                exit();
            } else {
                $err = 'Incorrect password.';
                log_action($conn, $row['id'], 'Failed login', 'Email provided but incorrect password');
            }
        } else {
            $err = 'Email not found.';
            log_action($conn, null, 'Failed login', 'Email not found: ' . $email);
        }
        $stmt->close();
    }
}
include 'includes/header.php';
?>

<style>
  body {
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%) !important;
    min-height: 100vh;
  }

  main {
    padding: 0 !important;
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .login-wrapper {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
  }

  .login-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 450px;
    width: 100%;
    padding: 2.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .login-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
  }

  .login-card h2 {
    color: #0f1e3d;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-align: center;
  }

  .form-group label {
    color: #0f1e3d;
    font-weight: 600;
  }

  .form-control {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 0.75rem;
    transition: all 0.3s ease;
  }

  .form-control:focus {
    border-color: #0f1e3d;
    box-shadow: 0 0 0 3px rgba(15, 30, 61, 0.1);
    outline: none;
  }

  .btn-login {
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);
    border: none;
    color: white;
    font-weight: 600;
    padding: 0.75rem;
    border-radius: 10px;
    width: 100%;
    margin-top: 1rem;
    transition: all 0.3s ease;
  }

  .btn-login:hover {
    transform: scale(1.02);
    box-shadow: 0 10px 30px rgba(15, 30, 61, 0.3);
    color: white;
  }

  .login-links {
    text-align: center;
    margin-top: 1.5rem;
  }

  .login-links a {
    color: #0f1e3d;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .login-links a:hover {
    color: #1a2f5a;
    text-decoration: underline;
  }

  .alert {
    border-radius: 10px;
    border: none;
    margin-bottom: 1.5rem;
  }
</style>

<div class="login-wrapper">
  <div class="login-card">
    <h2>Login</h2>

    <?php if($err): ?>
      <div class="alert alert-danger"><?php echo esc($err); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php" autocomplete="off">
      <div class="form-group mb-3">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required class="form-control" value="<?php echo esc($_POST['email'] ?? ''); ?>">
      </div>

      <div class="form-group mb-3">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required class="form-control">
      </div>

      <button type="submit" class="btn-login">Login</button>
    </form>

    <div class="login-links">
      <p class="mb-2">
        Don't have an account? <a href="register.php">Register here</a>
      </p>
      <p class="mb-0">
        <a href="forgot_password.php">Forgot your password?</a>
      </p>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
