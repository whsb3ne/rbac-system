<?php
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/audit.php'; // 🔹 add audit logging helper
require_login();

// Only Admin & Editor can delete
if ($_SESSION['role'] === 'Viewer') die("Access denied.");

$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    // Fetch file info first
    $stmt = $conn->prepare("SELECT filename, filepath FROM uploads WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $filename = $row['filename'];
        $filepath = $row['filepath'];

        // Delete file from server
        if (file_exists($filepath)) {
            if (unlink($filepath)) {
                // ✅ Log successful file deletion
                $uid = $_SESSION['user_id'];
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                log_action($conn, $uid, 'File deleted', "Deleted: $filename | Path: $filepath | IP: $ip");
            } else {
                // ⚠️ Failed to remove file from directory
                log_action($conn, $_SESSION['user_id'], 'File delete failed', "Could not unlink: $filename | Path: $filepath");
            }
        } else {
            // ⚠️ File missing on disk
            log_action($conn, $_SESSION['user_id'], 'File missing', "Attempted delete but file not found: $filename | Path: $filepath");
        }

        // Delete record from DB
        $del = $conn->prepare("DELETE FROM uploads WHERE id = ?");
        $del->bind_param("i", $id);
        if ($del->execute()) {
            log_action($conn, $_SESSION['user_id'], 'Upload record removed', "Removed DB record for file: $filename");
        } else {
            log_action($conn, $_SESSION['user_id'], 'Upload delete failed', "DB delete error: " . $del->error);
        }
        $del->close();
    } else {
        // No record found
        log_action($conn, $_SESSION['user_id'], 'Delete failed', "No upload record found for ID: $id");
    }
    $stmt->close();
}
header("Location: view_uploads.php");
exit;
?>
