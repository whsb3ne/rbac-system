<?php
session_start();
include 'includes/db.php';
include 'includes/audit.php'; // include the audit logging helper

// Log the logout event before session destroy
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'Unknown';
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if ($user_id) {
    log_action($conn, $user_id, 'User logged out', "Logout from IP: $ip");
} else {
    // if no session user (e.g., expired session)
    log_action($conn, null, 'Session ended', "Logout or timeout from IP: $ip");
}

// Destroy session completely
session_unset();
session_destroy();

// Redirect to login page
header("Location: index.php");
exit();
?>
