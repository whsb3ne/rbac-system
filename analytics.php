<?php
include 'includes/db.php';
include 'includes/functions.php';
require_admin();
include 'includes/header.php';

$total_users = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$total_uploads = $conn->query("SELECT COUNT(*) AS c FROM uploads")->fetch_assoc()['c'];
$total_logs = $conn->query("SELECT COUNT(*) AS c FROM audit_logs")->fetch_assoc()['c'];
$total_login_logs = $conn->query("SELECT COUNT(*) AS c FROM login_logs")->fetch_assoc()['c'];

$rolesData = [];
$res = $conn->query("SELECT r.role_name, COUNT(u.id) AS count FROM roles r LEFT JOIN users u ON r.id = u.role_id GROUP BY r.role_name");
while ($row = $res->fetch_assoc()) {
  $rolesData[$row['role_name']] = $row['count'];
}

$visibilityData = [];
$res2 = $conn->query("SELECT role_visibility, COUNT(id) AS count FROM uploads GROUP BY role_visibility");
while ($row = $res2->fetch_assoc()) {
  $visibilityData[$row['role_visibility']] = $row['count'];
}

$loginLabels = [];
$loginCounts = [];
$res3 = $conn->query("SELECT DATE(created_at) AS day, COUNT(*) AS count FROM login_logs WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(created_at)");
while ($row = $res3->fetch_assoc()) {
  $loginLabels[] = $row['day'];
  $loginCounts[] = $row['count'];
}
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
    margin-bottom: 2rem;
  }
  .card-modern .card-body {
    padding: 2rem;
  }
  .stat-box {
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    transition: all 0.3s ease;
  }
  .stat-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(15, 30, 61, 0.3);
  }
  .stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0.5rem 0;
  }
  .stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
  }
  .btn-pdf {
    background: linear-gradient(135deg, #0f1e3d 0%, #1a2f5a 100%);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  .btn-pdf:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 30px rgba(15, 30, 61, 0.3);
    color: white;
  }
</style>

<div class="page-header">
  <h1><i class="bi bi-graph-up me-2"></i>Dashboard Analytics</h1>
</div>

<div class="card-modern">
  <div class="card-body">
    <div class="text-end mb-3">
      <button id="downloadPdf" class="btn-pdf">
        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF Report
      </button>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="stat-box">
          <i class="bi bi-people-fill" style="font-size: 2rem;"></i>
          <div class="stat-value"><?= $total_users ?></div>
          <div class="stat-label">Total Users</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-box">
          <i class="bi bi-cloud-check-fill" style="font-size: 2rem;"></i>
          <div class="stat-value"><?= $total_uploads ?></div>
          <div class="stat-label">Uploads</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-box">
          <i class="bi bi-clipboard-data" style="font-size: 2rem;"></i>
          <div class="stat-value"><?= $total_logs ?></div>
          <div class="stat-label">Audit Logs</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-box">
          <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
          <div class="stat-value"><?= $total_login_logs ?></div>
          <div class="stat-label">Login Records</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-md-6">
    <div class="card-modern">
      <div class="card-body">
        <h5 class="mb-3"><i class="bi bi-person-lines-fill me-1"></i> Users by Role</h5>
        <canvas id="rolesChart" height="180"></canvas>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card-modern">
      <div class="card-body">
        <h5 class="mb-3"><i class="bi bi-cloud-upload me-1"></i> Uploads Visibility</h5>
        <canvas id="uploadsChart" height="180"></canvas>
      </div>
    </div>
  </div>

  <div class="col-md-12">
    <div class="card-modern">
      <div class="card-body">
        <h5 class="mb-3"><i class="bi bi-clock-history me-1"></i> Logins (Last 7 Days)</h5>
        <canvas id="loginsChart" height="100"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js + PDF Libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
const roleLabels = <?= json_encode(array_keys($rolesData)) ?>;
const roleCounts = <?= json_encode(array_values($rolesData)) ?>;
const uploadLabels = <?= json_encode(array_keys($visibilityData)) ?>;
const uploadCounts = <?= json_encode(array_values($visibilityData)) ?>;
const loginLabels = <?= json_encode($loginLabels) ?>;
const loginCounts = <?= json_encode($loginCounts) ?>;

// Users by Role (Doughnut)
new Chart(document.getElementById('rolesChart'), {
  type: 'doughnut',
  data: { labels: roleLabels, datasets: [{ data: roleCounts, backgroundColor: ['#0d6efd', '#198754', '#ffc107'] }] },
  options: { plugins: { legend: { position: 'bottom' } } }
});

// Uploads by Visibility (Pie)
new Chart(document.getElementById('uploadsChart'), {
  type: 'pie',
  data: { labels: uploadLabels, datasets: [{ data: uploadCounts, backgroundColor: ['#0dcaf0', '#6f42c1', '#fd7e14', '#198754'] }] },
  options: { plugins: { legend: { position: 'bottom' } } }
});

// Logins per day (Bar)
new Chart(document.getElementById('loginsChart'), {
  type: 'bar',
  data: {
    labels: loginLabels,
    datasets: [{
      label: 'Logins',
      data: loginCounts,
      backgroundColor: '#0d6efd'
    }]
  },
  options: {
    scales: { y: { beginAtZero: true } },
    plugins: { legend: { display: false } }
  }
});

// 🔽 Export Dashboard to PDF
document.getElementById('downloadPdf').addEventListener('click', () => {
  const { jsPDF } = window.jspdf;
  const dashboard = document.querySelector('.main-card');

  html2canvas(dashboard, { backgroundColor: '#ffffff', scale: 2 }).then(canvas => {
    const imgData = canvas.toDataURL('image/png');
    const pdf = new jsPDF('p', 'mm', 'a4');
    const pageWidth = pdf.internal.pageSize.getWidth();
    const pageHeight = pdf.internal.pageSize.getHeight();
    const imgWidth = pageWidth - 20;
    const imgHeight = canvas.height * imgWidth / canvas.width;

    let y = 15;
    pdf.setFontSize(16);
    pdf.text("RBAC System Analytics Report", pageWidth / 2, y, { align: "center" });
    pdf.addImage(imgData, 'PNG', 10, 25, imgWidth, imgHeight);

    pdf.setFontSize(10);
    const date = new Date().toLocaleString();
    pdf.text(`Generated on: ${date}`, 10, pageHeight - 10);

    pdf.save('RBAC_Analytics_Report.pdf');
  });
});
</script>

<?php include 'includes/footer.php'; ?>
