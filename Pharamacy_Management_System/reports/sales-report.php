<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}
include("../includes/db.php");

// Validate and sanitize date inputs
$from = (isset($_GET['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from'])) ? $_GET['from'] : date('Y-m-01');
$to   = (isset($_GET['to'])   && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to']))   ? $_GET['to']   : date('Y-m-d');

// Escape for safe use in queries
$from_safe = mysqli_real_escape_string($conn, $from);
$to_safe   = mysqli_real_escape_string($conn, $to);

// Total revenue
$rev_res       = mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) AS t FROM sales WHERE DATE(sale_date) BETWEEN '$from_safe' AND '$to_safe'");
$total_revenue = 0;
if ($rev_res) {
    $rev_row = mysqli_fetch_assoc($rev_res);
    $total_revenue = $rev_row['t'];
}

// Average sale
$avg_res  = mysqli_query($conn, "SELECT COALESCE(AVG(total_amount),0) AS t FROM sales WHERE DATE(sale_date) BETWEEN '$from_safe' AND '$to_safe'");
$avg_sale = 0;
if ($avg_res) {
    $avg_row  = mysqli_fetch_assoc($avg_res);
    $avg_sale = $avg_row['t'];
}

// All sales rows
$sales_res   = mysqli_query($conn, "SELECT * FROM sales WHERE DATE(sale_date) BETWEEN '$from_safe' AND '$to_safe' ORDER BY sale_date DESC");
$total_sales = ($sales_res) ? mysqli_num_rows($sales_res) : 0;

// Pre-load item counts for all sales (avoids per-row queries)
$item_counts = array();
$ic_res = mysqli_query($conn, "SELECT sale_id, COUNT(*) AS c FROM sale_items GROUP BY sale_id");
if ($ic_res) {
    while ($ic = mysqli_fetch_assoc($ic_res)) {
        $item_counts[$ic['sale_id']] = (int)$ic['c'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report | PharmaCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content">

    <div class="topbar">
        <h5><i class="fa-solid fa-chart-bar me-2 text-primary"></i>Sales Report</h5>
        <a href="stock-report.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-boxes-stacked me-1"></i> Stock Report
        </a>
    </div>

    <div class="page-body">

        <!-- Date Filter -->
        <div class="form-card mb-4" style="padding:20px 24px">
            <form method="GET" class="d-flex align-items-end gap-3 flex-wrap">
                <div>
                    <label class="form-label mb-1">From Date</label>
                    <input type="date" name="from" class="form-control form-control-sm"
                           value="<?php echo htmlspecialchars($from, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div>
                    <label class="form-label mb-1">To Date</label>
                    <input type="date" name="to" class="form-control form-control-sm"
                           value="<?php echo htmlspecialchars($to, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                <a href="sales-report.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Reset</a>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#16a34a,#4ade80)">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <h6>Total Sales</h6>
                        <h3><?php echo (int)$total_sales; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                    <div>
                        <h6>Total Revenue</h6>
                        <h3>$<?php echo number_format((float)$total_revenue, 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#0891b2,#67e8f9)">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div>
                        <h6>Avg Sale Value</h6>
                        <h3>$<?php echo number_format((float)$avg_sale, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Table -->
        <div class="table-card">
            <div class="card-header">
                <h5>
                    <i class="fa-solid fa-list me-2"></i>
                    Sales &mdash;
                    <?php echo date('d M Y', strtotime($from)); ?>
                    to
                    <?php echo date('d M Y', strtotime($to)); ?>
                </h5>
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
                            <th>Date &amp; Time</th>
                            <th>Items</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($sales_res && $total_sales > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($sales_res)): ?>
                        <tr>
                            <td>
                                <span class="text-muted">#<?php echo (int)$row['id']; ?></span>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['customer_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                            </td>
                            <td>
                                <strong class="text-success">
                                    $<?php echo number_format((float)$row['total_amount'], 2); ?>
                                </strong>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?php echo htmlspecialchars($row['payment_method'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td class="text-muted" style="font-size:13px">
                                <?php echo date('d M Y, H:i', strtotime($row['sale_date'])); ?>
                            </td>
                            <td>
                                <?php
                                $cnt = isset($item_counts[$row['id']]) ? $item_counts[$row['id']] : 0;
                                ?>
                                <span class="qty-badge qty-ok"><?php echo $cnt; ?> items</span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fa-solid fa-chart-bar"></i>
                                    <p>No sales found for this period</p>
                                    <a href="../sales/new-sale.php" class="btn btn-primary btn-sm rounded-pill px-4">
                                        Make a Sale
                                    </a>
                                </div>
                            </td>
                        </tr>
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
    var q = this.value.toLowerCase();
    var rows = document.querySelectorAll('#salesTable tbody tr');
    for (var i = 0; i < rows.length; i++) {
        rows[i].style.display = rows[i].textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    }
});
</script>
</body>
</html>
