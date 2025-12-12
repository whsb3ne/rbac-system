<?php
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/audit.php'; // 🔹 add audit logging
require_login();

// Only Admin and Editor allowed
if ($_SESSION['role'] === 'Viewer') {
  die("Access denied.");
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    if ($file['error'] === 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $targetPath = $uploadDir . time() . "_" . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $filename = $file['name'];
            $visibility = $_POST['visibility'] ?? 'All';
            $uid = $_SESSION['user_id'];

            $stmt = $conn->prepare("INSERT INTO uploads (filename, filepath, uploaded_by, role_visibility) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $filename, $targetPath, $uid, $visibility);

            if ($stmt->execute()) {
                $msg = "✅ File uploaded successfully!";
                // 🔹 Log the upload event
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                log_action($conn, $uid, 'File uploaded', "File: $filename | Path: $targetPath | Visibility: $visibility | IP: $ip");
            } else {
                $msg = "❌ Database error: " . $stmt->error;
                log_action($conn, $_SESSION['user_id'], 'Upload failed', "DB error: " . $stmt->error);
            }
            $stmt->close();
        } else {
            $msg = "❌ Error uploading file.";
            log_action($conn, $_SESSION['user_id'], 'Upload failed', 'move_uploaded_file() returned false');
        }
    } else {
        $msg = "❌ Upload error: " . $file['error'];
        log_action($conn, $_SESSION['user_id'], 'Upload failed', 'File error code: ' . $file['error']);
    }
}

include 'includes/header.php';
?>

<style>
  main {
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%) !important;
    min-height: 100vh;
  }
  .page-header {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  }
  .page-header h1 {
    color: #0f1e3d;
    font-weight: 700;
    margin: 0;
  }
  .card-modern {
    background: white;
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 0 auto 2rem;
  }
  .card-modern .card-body {
    padding: 2rem;
  }
  .form-label {
    color: #0f1e3d;
    font-weight: 600;
    margin-bottom: 0.5rem;
  }
  .form-control,
  .form-select {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 0.75rem;
    transition: all 0.3s ease;
  }
  .form-control:focus,
  .form-select:focus {
    border-color: #0f1e3d;
    box-shadow: 0 0 0 3px rgba(15, 30, 61, 0.1);
    outline: none;
  }
  .btn-upload {
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);
    border: none;
    color: white;
    font-weight: 600;
    width: 100%;
    padding: 0.75rem;
    border-radius: 12px;
    margin-top: 1rem;
    transition: all 0.3s ease;
  }
  .btn-upload:hover {
    transform: scale(1.02);
    box-shadow: 0 10px 30px rgba(15, 30, 61, 0.3);
    color: white;
  }
</style>

<div class="page-header">
  <h1><i class="bi bi-cloud-upload-fill me-2"></i>Upload File</h1>
</div>

<div class="card-modern">
  <div class="card-body">
    <?php if ($msg): ?>
      <div class="alert alert-info" style="border-radius: 12px; background: #e7f3ff; border: 2px solid #0f1e3d; color: #0f1e3d;"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label"><i class="bi bi-file-earmark me-1"></i>Choose File</label>
        <input type="file" name="file" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label"><i class="bi bi-eye me-1"></i>Visible To</label>
        <select name="visibility" class="form-select">
          <option value="All">All Roles</option>
          <option value="Viewer">Viewers Only</option>
          <option value="Editor">Editors Only</option>
          <option value="Admin">Admins Only</option>
        </select>
      </div>
      <button class="btn btn-upload"><i class="bi bi-upload me-1"></i>Upload File</button>
    </form>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
