<?php
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/audit.php'; // 🔹 add audit logging helper
require_admin();

$user_id = intval($_GET['id'] ?? 0);
if (!$user_id) { 
    header('Location: manage_users.php'); 
    exit(); 
}

if ($user_id == $_SESSION['user_id']) { 
    echo '❌ You cannot delete your own account.';
    log_action($conn, $_SESSION['user_id'], 'Delete self-attempt', 'Admin tried to delete their own account.');
    exit(); 
}

// fetch user details before deletion for logging
$stmt_user = $conn->prepare('SELECT username, email FROM users WHERE id = ?');
$stmt_user->bind_param('i', $user_id);
$stmt_user->execute();
$user_data = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

if (!$user_data) {
    log_action($conn, $_SESSION['user_id'], 'Delete failed', "Attempted to delete non-existent user (ID: $user_id)");
    header('Location: manage_users.php');
    exit();
}

// proceed with delete
$stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);

if ($stmt->execute()) {
    // ✅ Log successful deletion
    $admin_id = $_SESSION['user_id'];
    $admin_name = $_SESSION['username'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $deleted_user = $user_data['username'] . " (" . $user_data['email'] . ")";
    log_action($conn, $admin_id, 'User deleted', "Admin: $admin_name deleted user ID $user_id [$deleted_user] from IP: $ip");

    header('Location: manage_users.php');
    exit();
} else {
    // ⚠️ Log failure
    log_action($conn, $_SESSION['user_id'], 'User deletion failed', "DB error: " . $stmt->error);
    echo 'Error: ' . $stmt->error;
}
$stmt->close();
?>
