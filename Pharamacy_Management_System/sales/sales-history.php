<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit(); }
include("../includes/db.php");

$sales   = mysqli_query($conn, "SELECT * FROM sales ORDER BY sale_date DESC");
$total_r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) t FROM sales"))['t'];
$count   = mysqli_num_rows($sales);
mysqli_data_seek($sales, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales History | PharmaCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fa-solid fa-receipt me-2 text-primary"></i>Sales History</h5>
        <a href="new-sale.php" class="btn btn-primary btn-sm rounded-pill px-4">
            <i class="fa-solid fa-plus me-1"></i> New Sale
        </a>
    </div>

    <div class="page-body">

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#16a34a,#4ade80)">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div><h6>Total Sales</h6><h3><?= $count ?></h3></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                    <div><h6>Total Revenue</h6><h3>$<?= number_format($total_r, 2) ?></h3></div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-list me-2"></i>All Sales</h5>
                <div class="search-box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search..." class="form-control form-control-sm">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" id="salesTable">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Items</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $has = false;
                    while ($row = mysqli_fetch_assoc($sales)):
                        $has = true;
                        $items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM sale_items WHERE sale_id={$row['id']}"))['c'];
                    ?>
                    <tr>
                        <td><span class="badge bg-light text-dark border">#<?= $row['id'] ?></span></td>
                        <td><strong><?= htmlspecialchars($row['customer_name']) ?></strong></td>
                        <td><strong class="text-success">$<?= number_format($row['total_amount'], 2) ?></strong></td>
                        <td><span class="badge bg-light text-dark"><?= htmlspecialchars($row['payment_method']) ?></span></td>
                        <td class="text-muted" style="font-size:13px"><?= date('d M Y, H:i', strtotime($row['sale_date'])) ?></td>
                        <td><span class="qty-badge qty-ok"><?= $items ?> items</span></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if (!$has): ?>
                    <tr><td colspan="6">
                        <div class="empty-state">
                            <i class="fa-solid fa-receipt"></i>
                            <p>No sales recorded yet</p>
                            <a href="new-sale.php" class="btn btn-primary btn-sm rounded-pill px-4">Make First Sale</a>
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
    document.querySelectorAll('#salesTable tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
</body>
</html>
