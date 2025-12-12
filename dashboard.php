<?php
include 'includes/db.php';
include 'includes/functions.php';
require_login();
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
  }

  .dashboard-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
  }

  .dashboard-card {
    background: white;
    border: none;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    max-width: 500px;
    width: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .dashboard-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
  }

  .dashboard-header {
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);
    padding: 3rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .dashboard-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
  }

  .avatar-icon {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: #1a1a1a;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 3.5rem;
    border: 3px solid rgba(255, 255, 255, 0.3);
    position: relative;
    z-index: 1;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  }

  .welcome-text {
    color: white;
    position: relative;
    z-index: 1;
  }

  .welcome-text h2 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .role-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.25);
    color: white;
    padding: 0.6rem 1.5rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    border: 2px solid rgba(255, 255, 255, 0.3);
  }

  .dashboard-body {
    padding: 2rem;
  }

  .action-section {
    margin-top: 1.5rem;
  }

  .action-section h5 {
    color: #0f1e3d;
    font-weight: 700;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .login-logs-btn {
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);
    border: none;
    color: white;
    padding: 1rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
  }

  .login-logs-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 30px rgba(15, 30, 61, 0.3);
    color: white;
    text-decoration: none;
  }

  .login-logs-btn::before {
    content: '📋';
    font-size: 1.2rem;
  }
</style>

<div class="dashboard-wrapper">
  <div class="dashboard-card">
    <div class="dashboard-header">
      <div class="avatar-icon">👤</div>
      <div class="welcome-text">
        <h2>Welcome, <?php echo esc($_SESSION['username']); ?>!</h2>
        <span class="role-badge"><?php echo esc($_SESSION['role']); ?></span>
      </div>
    </div>

    <div class="dashboard-body">
      <?php if ($_SESSION['role'] === 'Admin'): ?>
        <div class="action-section">
          <h5>Admin Actions</h5>
          <a href="login_logs.php" class="login-logs-btn">View Login Logs</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>


