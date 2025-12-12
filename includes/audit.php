<?php
// includes/audit.php
if (session_status() === PHP_SESSION_NONE) session_start();

function log_action($conn, $user_id, $action, $details = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $action, $details, $ip);
    $stmt->execute();
    $stmt->close();
}
?>
