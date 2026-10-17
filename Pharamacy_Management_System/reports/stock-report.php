<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit(); }
include("../includes/db.php");

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if ($filter === 'low') {
    $where = "WHERE quantity < 10";
} elseif ($filter === 'expiring') {
    $where = "WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
} elseif ($filter === 'expired') {
    $where = "WHERE expiry_date < CURDATE()";
} else {
    $where = "";
}

$medicines = mysqli_query($conn, "SELECT * FROM medicines $where ORDER BY quantity ASC");
$total     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM medicines"))['c'];
$low       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM medicines WHERE quantity < 10"))['c'];
$expiring  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM medicines WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)"))['c'];
$expired   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM medicines WHERE expiry_date < CURDATE()"))['c'];
$inv_val   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(price*quantity),0) t FROM medicines"))['t'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Report | PharmaCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fa-solid fa-boxes-stacked me-2 text-primary"></i>Stock Report</h5>
        <a href="sales-report.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-chart-bar me-1"></i> Sales Report
        </a>
    </div>

    <div class="page-body">

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#2563eb,#60a5fa)">
                        <i class="fa-solid fa-pills"></i>
                    </div>
                    <div><h6>Total Items</h6><h3><?= $total ?></h3></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#dc2626,#f87171)">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div><h6>Low Stock</h6><h3><?= $low ?></h3></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#d97706,#fbbf24)">
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                    <div><h6>Expiring (30d)</h6><h3><?= $expiring ?></h3></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#16a34a,#4ade80)">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                    <div><h6>Inventory Value</h6><h3>$<?= number_format($inv_val, 0) ?></h3></div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="d-flex gap-2 mb-3 flex-wrap">
            <a href="?filter=all"      class="btn btn-sm rounded-pill px-4 <?= $filter==='all'      ? 'btn-primary' : 'btn-outline-secondary' ?>">All (<?= $total ?>)</a>
            <a href="?filter=low"      class="btn btn-sm rounded-pill px-4 <?= $filter==='low'      ? 'btn-danger'  : 'btn-outline-danger' ?>">Low Stock (<?= $low ?>)</a>
            <a href="?filter=expiring" class="btn btn-sm rounded-pill px-4 <?= $filter==='expiring' ? 'btn-warning text-white' : 'btn-outline-warning' ?>">Expiring Soon (<?= $expiring ?>)</a>
            <a href="?filter=expired"  class="btn btn-sm rounded-pill px-4 <?= $filter==='expired'  ? 'btn-dark'    : 'btn-outline-dark' ?>">Expired (<?= $expired ?>)</a>
        </div>

        <!-- Table -->
        <div class="table-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-list me-2"></i>Stock Overview</h5>
                <div class="search-box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search..." class="form-control form-control-sm">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" id="stockTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Medicine</th>
                            <th>Category</th>
                            <th>Batch</th>
                            <th>Expiry</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 1; $has = false;
                    while ($row = mysqli_fetch_assoc($medicines)):
                        $has = true;
                        $exp_ts  = !empty($row['expiry_date']) ? strtotime($row['expiry_date']) : false;
                        $is_exp  = $exp_ts !== false && $exp_ts < time();
                        $is_soon = $exp_ts !== false && !$is_exp && $exp_ts < strtotime('+30 days');
                        $is_low  = $row['quantity'] < 10;
                        $value   = $row['price'] * $row['quantity'];
                    ?>
                    <tr>
                        <td class="text-muted"><?= $i++ ?></td>
                        <td><strong><?= htmlspecialchars($row['medicine_name']) ?></strong></td>
                        <td class="text-muted"><?= htmlspecialchars($row['category'] ?? '—') ?></td>
                        <td><code><?= htmlspecialchars($row['batch_no'] ?? '—') ?></code></td>
                        <td class="<?= $is_exp ? 'text-danger fw-bold' : ($is_soon ? 'text-warning fw-semibold' : '') ?>">
                            <?= ($exp_ts !== false) ? date('d M Y', $exp_ts) : '—' ?>
                        </td>
                        <td><span class="qty-badge <?= $is_low ? 'qty-low' : 'qty-ok' ?>"><?= $row['quantity'] ?></span></td>
                        <td>$<?= number_format($row['price'], 2) ?></td>
                        <td><strong>$<?= number_format($value, 2) ?></strong></td>
                        <td>
                            <?php if ($is_exp): ?>
                                <span class="badge bg-danger">Expired</span>
                            <?php elseif ($is_soon): ?>
                                <span class="badge bg-warning text-dark">Expiring Soon</span>
                            <?php elseif ($is_low): ?>
                                <span class="badge bg-danger">Low Stock</span>
                            <?php else: ?>
                                <span class="badge bg-success">OK</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if (!$has): ?>
                    <tr><td colspan="9">
                        <div class="empty-state">
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <p>No medicines found for this filter</p>
                        </div>
                    </td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('searchInput').addEventListener('keyup', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#stockTable tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
</body>
</html>
