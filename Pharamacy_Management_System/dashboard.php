<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include("includes/db.php");

$medicines  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM medicines"))['c'];
$low_stock  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM medicines WHERE quantity < 10"))['c'];
$suppliers  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM suppliers"))['c'];
$sales_today = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM sales WHERE DATE(sale_date)=CURDATE()"))['c'];
$revenue    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) t FROM sales"))['t'];
$inv_value  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(price*quantity),0) t FROM medicines"))['t'];
$recent_sales = mysqli_query($conn, "SELECT * FROM sales ORDER BY sale_date DESC LIMIT 5");
$low_meds = mysqli_query($conn, "SELECT * FROM medicines WHERE quantity < 10 ORDER BY quantity ASC LIMIT 5");
$expiring = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM medicines WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | PharmaCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #0a0e27;
            color: #fff;
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-animated {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 50%, #0f1729 100%);
            z-index: -1;
        }
        .bg-animated::before {
            content: ''; position: absolute; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(59,130,246,0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            animation: gridMove 20s linear infinite;
        }
        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(40px, 40px); }
        }

        /* Sidebar */
        .sidebar {
            width: 260px; min-height: 100vh; position: fixed; top: 0; left: 0;
            background: rgba(10,14,39,0.8); backdrop-filter: blur(20px);
            border-right: 1px solid rgba(59,130,246,0.1);
            padding: 0; z-index: 100; display: flex; flex-direction: column;
        }
        .sidebar .brand {
            padding: 24px; border-bottom: 1px solid rgba(59,130,246,0.1);
            font-size: 22px; font-weight: 800; color: #fff;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar .nav-label {
            color: rgba(255,255,255,0.4); font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px; padding: 24px 24px 8px;
        }
        .sidebar a.nav-link {
            display: flex; align-items: center; gap: 14px;
            color: rgba(255,255,255,0.7); text-decoration: none;
            padding: 12px 24px; font-size: 14px; font-weight: 500;
            transition: all 0.3s; border-left: 3px solid transparent;
        }
        .sidebar a.nav-link:hover {
            background: rgba(59,130,246,0.1); color: #fff; padding-left: 28px;
        }
        .sidebar a.nav-link.active {
            background: rgba(59,130,246,0.15); color: #fff;
            border-left-color: #3b82f6; font-weight: 600;
        }
        .sidebar a.nav-link i { width: 20px; text-align: center; font-size: 16px; }
        .sidebar .logout-link {
            margin-top: auto; border-top: 1px solid rgba(59,130,246,0.1);
        }
        .sidebar .logout-link a { color: #f87171 !important; }
        .sidebar .logout-link a:hover { background: rgba(220,38,38,0.15) !important; }

        /* Main Content */
        .main-content { margin-left: 260px; min-height: 100vh; }

        /* Topbar */
        .topbar {
            background: rgba(10,14,39,0.8); backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(59,130,246,0.1);
            padding: 18px 32px; display: flex; justify-content: space-between;
            align-items: center; position: sticky; top: 0; z-index: 99;
        }
        .topbar h5 {
            margin: 0; font-weight: 700; font-size: 20px;
            background: linear-gradient(135deg, #fff, #3b82f6);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .topbar .user-info {
            display: flex; align-items: center; gap: 16px;
        }
        .topbar .badge {
            background: rgba(59,130,246,0.2); color: #3b82f6;
            border: 1px solid rgba(59,130,246,0.3);
            padding: 8px 16px; border-radius: 50px; font-weight: 600;
        }

        /* Page Body */
        .page-body { padding: 32px; }

        /* Stat Cards */
        .stat-card {
            background: rgba(255,255,255,0.03); backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1); border-radius: 20px;
            padding: 24px; display: flex; align-items: center; gap: 20px;
            transition: all 0.4s; animation: fadeUp 0.6s ease both;
            position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0;
            width: 100%; height: 100%; background: linear-gradient(135deg, rgba(59,130,246,0.1), transparent);
            opacity: 0; transition: opacity 0.4s;
        }
        .stat-card:hover::before { opacity: 1; }
        .stat-card:hover {
            transform: translateY(-8px);
            border-color: rgba(59,130,246,0.5);
            box-shadow: 0 20px 60px rgba(59,130,246,0.3);
        }
        .stat-icon {
            width: 64px; height: 64px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: #fff; flex-shrink: 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .stat-card h6 { margin: 0; color: rgba(255,255,255,0.6); font-size: 13px; font-weight: 500; }
        .stat-card h3 { margin: 6px 0 0; font-weight: 800; font-size: 32px; color: #fff; }

        /* Table Card */
        .table-card {
            background: rgba(255,255,255,0.03); backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1); border-radius: 20px;
            overflow: hidden; animation: fadeUp 0.6s ease 0.2s both;
        }
        .table-card .card-header {
            background: rgba(255,255,255,0.02); padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex; justify-content: space-between; align-items: center;
        }
        .table-card .card-header h5 {
            margin: 0; font-weight: 700; font-size: 18px; color: #fff;
        }
        .table thead th {
            background: rgba(255,255,255,0.02); color: rgba(255,255,255,0.6);
            font-size: 12px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1px; border: none; padding: 14px 16px;
        }
        .table tbody tr {
            border-bottom: 1px solid rgba(255,255,255,0.05);
            transition: background 0.2s;
        }
        .table tbody tr:hover { background: rgba(59,130,246,0.05); }
        .table tbody td {
            padding: 14px 16px; vertical-align: middle;
            font-size: 14px; color: rgba(255,255,255,0.8); border: none;
        }

        /* Badges */
        .qty-badge {
            display: inline-block; padding: 5px 14px;
            border-radius: 20px; font-size: 12px; font-weight: 600;
        }
        .qty-low  { background: rgba(220,38,38,0.2); color: #f87171; border: 1px solid rgba(220,38,38,0.3); }
        .qty-ok   { background: rgba(34,197,94,0.2); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border: none; box-shadow: 0 4px 15px rgba(59,130,246,0.4);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59,130,246,0.6);
        }
        .btn-outline-primary {
            border-color: rgba(59,130,246,0.5); color: #3b82f6;
        }
        .btn-outline-primary:hover {
            background: rgba(59,130,246,0.1); border-color: #3b82f6; color: #3b82f6;
        }
        .btn-outline-danger {
            border-color: rgba(220,38,38,0.5); color: #f87171;
        }
        .btn-outline-danger:hover {
            background: rgba(220,38,38,0.1); border-color: #dc2626; color: #f87171;
        }

        /* Empty State */
        .empty-state {
            text-align: center; padding: 40px 20px;
            color: rgba(255,255,255,0.4);
        }
        .empty-state i { font-size: 48px; margin-bottom: 12px; display: block; }

        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .sidebar { width: 0; overflow: hidden; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="bg-animated"></div>

<!-- Sidebar -->
<div class="sidebar">
    <div class="brand"><i class="fa-solid fa-pills"></i> PharmaCare</div>
    <div class="nav-label">Main</div>
    <a href="dashboard.php" class="nav-link active"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <div class="nav-label">Medicines</div>
    <a href="medicines/add-medicine.php" class="nav-link"><i class="fa-solid fa-plus-circle"></i> Add Medicine</a>
    <a href="medicines/manage-medicine.php" class="nav-link"><i class="fa-solid fa-capsules"></i> Manage Medicines</a>
    <div class="nav-label">Sales</div>
    <a href="sales/new-sale.php" class="nav-link"><i class="fa-solid fa-cart-plus"></i> New Sale</a>
    <a href="sales/sales-history.php" class="nav-link"><i class="fa-solid fa-receipt"></i> Sales History</a>
    <div class="nav-label">Other</div>
    <a href="suppliers/manage-supplier.php" class="nav-link"><i class="fa-solid fa-truck"></i> Suppliers</a>
    <a href="reports/sales-report.php" class="nav-link"><i class="fa-solid fa-chart-bar"></i> Reports</a>
    <div class="logout-link">
        <a href="logout.php" class="nav-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="topbar">
        <h5><i class="fa-solid fa-gauge me-2"></i>Dashboard</h5>
        <div class="user-info">
            <span style="color: rgba(255,255,255,0.6); font-size: 13px;">
                <i class="fa-regular fa-clock me-1"></i><?php echo date('D, d M Y'); ?>
            </span>
            <span class="badge">
                <i class="fa-solid fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
            </span>
        </div>
    </div>

    <div class="page-body">
        <!-- Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4 col-6">
                <div class="stat-card" style="animation-delay:0s">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#3b82f6,#60a5fa)">
                        <i class="fa-solid fa-pills"></i>
                    </div>
                    <div><h6>Medicines</h6><h3><?php echo $medicines; ?></h3></div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="stat-card" style="animation-delay:0.1s">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#dc2626,#f87171)">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div><h6>Low Stock</h6><h3><?php echo $low_stock; ?></h3></div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="stat-card" style="animation-delay:0.2s">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#d97706,#fbbf24)">
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                    <div><h6>Expiring Soon</h6><h3><?php echo $expiring; ?></h3></div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="stat-card" style="animation-delay:0.3s">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#16a34a,#4ade80)">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div><h6>Sales Today</h6><h3><?php echo $sales_today; ?></h3></div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="stat-card" style="animation-delay:0.4s">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                    <div><h6>Total Revenue</h6><h3>$<?php echo number_format($revenue, 0); ?></h3></div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="stat-card" style="animation-delay:0.5s">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#0891b2,#67e8f9)">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <div><h6>Suppliers</h6><h3><?php echo $suppliers; ?></h3></div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Recent Sales -->
            <div class="col-lg-7">
                <div class="table-card">
                    <div class="card-header">
                        <h5><i class="fa-solid fa-receipt me-2"></i>Recent Sales</h5>
                        <a href="sales/sales-history.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr><th>#</th><th>Customer</th><th>Amount</th><th>Payment</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                            <?php if (mysqli_num_rows($recent_sales) > 0):
                                while ($s = mysqli_fetch_assoc($recent_sales)): ?>
                                <tr>
                                    <td><span style="color:rgba(255,255,255,0.5)">#<?php echo $s['id']; ?></span></td>
                                    <td><?php echo htmlspecialchars($s['customer_name']); ?></td>
                                    <td><strong style="color:#4ade80">$<?php echo number_format($s['total_amount'], 2); ?></strong></td>
                                    <td><span class="badge bg-secondary"><?php echo $s['payment_method']; ?></span></td>
                                    <td style="color:rgba(255,255,255,0.5);font-size:12px"><?php echo date('d M, H:i', strtotime($s['sale_date'])); ?></td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-receipt"></i><p>No sales yet</p></div></td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="col-lg-5">
                <div class="table-card">
                    <div class="card-header">
                        <h5><i class="fa-solid fa-triangle-exclamation me-2" style="color:#f87171"></i>Low Stock Alert</h5>
                        <a href="medicines/manage-medicine.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">Manage</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th>Medicine</th><th>Category</th><th>Qty</th></tr></thead>
                            <tbody>
                            <?php if (mysqli_num_rows($low_meds) > 0):
                                while ($m = mysqli_fetch_assoc($low_meds)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($m['medicine_name']); ?></strong></td>
                                    <td style="color:rgba(255,255,255,0.5)"><?php echo htmlspecialchars($m['category'] ?? '—'); ?></td>
                                    <td><span class="qty-badge qty-low"><?php echo $m['quantity']; ?></span></td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="3"><div class="empty-state" style="padding:30px"><i class="fa-solid fa-check-circle" style="color:#4ade80;font-size:36px"></i><p class="mt-2">All stock levels OK</p></div></td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="table-card">
                    <div class="card-header"><h5><i class="fa-solid fa-bolt me-2"></i>Quick Actions</h5></div>
                    <div class="p-4 d-flex flex-wrap gap-3">
                        <a href="medicines/add-medicine.php" class="btn btn-primary rounded-pill px-4"><i class="fa-solid fa-plus me-2"></i>Add Medicine</a>
                        <a href="sales/new-sale.php" class="btn btn-success rounded-pill px-4"><i class="fa-solid fa-cart-plus me-2"></i>New Sale</a>
                        <a href="suppliers/add-supplier.php" class="btn btn-info text-white rounded-pill px-4"><i class="fa-solid fa-truck me-2"></i>Add Supplier</a>
                        <a href="reports/sales-report.php" class="btn btn-warning text-white rounded-pill px-4"><i class="fa-solid fa-chart-bar me-2"></i>View Reports</a>
                        <a href="reports/stock-report.php" class="btn btn-secondary rounded-pill px-4"><i class="fa-solid fa-boxes-stacked me-2"></i>Stock Report</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
