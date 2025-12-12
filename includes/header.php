<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RBAC System</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body style="margin: 0; padding: 0;">
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm" style="position: fixed; top: 0; width: 100%; left: 0; z-index: 1000; padding: 0 2rem;">
    <div style="width: 100%; display: flex; align-items: center; justify-content: space-between;">
      <a class="navbar-brand" href="dashboard.php" style="margin: 0;">RBAC System</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navmenu">
        <ul class="navbar-nav ms-auto">
          <?php if(isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link <?php if($current_page=='dashboard.php') echo 'active'; ?>" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php if($current_page=='view_uploads.php') echo 'active'; ?>" href="view_uploads.php">Uploads</a>
            </li>
            <?php if($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Editor'): ?>
              <li class="nav-item">
                <a class="nav-link <?php if($current_page=='upload.php') echo 'active'; ?>" href="upload.php">Upload File</a>
              </li>
            <?php endif; ?>
            <?php if($_SESSION['role'] === 'Admin'): ?>
              <li class="nav-item">
                <a class="nav-link <?php if($current_page=='manage_users.php') echo 'active'; ?>" href="manage_users.php">Manage Users</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php if($current_page=='audit_logs.php') echo 'active'; ?>" href="audit_logs.php">Audit Logs</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php if($current_page=='analytics.php') echo 'active'; ?>" href="analytics.php">Analytics</a>
              </li>
            <?php endif; ?>
            <li class="nav-item">
              <a class="nav-link <?php if($current_page=='profile.php') echo 'active'; ?>" href="profile.php">Profile</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php if($current_page=='logout.php') echo 'active'; ?>" href="logout.php">Logout</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link <?php if($current_page=='index.php') echo 'active'; ?>" href="index.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php if($current_page=='register.php') echo 'active'; ?>" href="register.php">Register</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
  <main style="margin-top: 60px; padding: 2rem; background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%); min-height: calc(100vh - 60px); width: 100%;">
