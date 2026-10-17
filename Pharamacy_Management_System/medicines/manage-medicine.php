<?php
include("../includes/db.php");

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM medicines WHERE id=$id");
    header("Location: manage-medicine.php?msg=deleted");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM medicines ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Medicines | Pharmacy System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f0f4ff; font-family: 'Segoe UI', sans-serif; }

        /* Sidebar */
        .sidebar {
            width: 240px; min-height: 100vh;
            background: linear-gradient(180deg, #1e3a8a, #2563eb);
            padding: 30px 0; position: fixed; top: 0; left: 0;
            box-shadow: 4px 0 15px rgba(0,0,0,0.15);
            z-index: 100;
        }
        .sidebar .brand {
            color: #fff; font-size: 20px; font-weight: 700;
            padding: 0 24px 24px; border-bottom: 1px solid rgba(255,255,255,0.15);
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar a {
            display: flex; align-items: center; gap: 12px;
            color: rgba(255,255,255,0.8); text-decoration: none;
            padding: 12px 24px; font-size: 14.5px;
            transition: all 0.25s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.15);
            color: #fff; padding-left: 30px;
        }
        .sidebar a i { width: 18px; text-align: center; }

        /* Main */
        .main-content { margin-left: 240px; padding: 0; }

        /* Topbar */
        .topbar {
            background: #fff; padding: 16px 32px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06); position: sticky; top: 0; z-index: 99;
        }
        .topbar h5 { margin: 0; font-weight: 700; color: #1e3a8a; font-size: 18px; }

        /* Page body */
        .page-body { padding: 28px 32px; }

        /* Stats cards */
        .stat-card {
            background: #fff; border-radius: 16px;
            padding: 22px 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            display: flex; align-items: center; gap: 18px;
            animation: fadeUp 0.5s ease both;
        }
        .stat-icon {
            width: 54px; height: 54px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: #fff; flex-shrink: 0;
        }
        .stat-card h6 { margin: 0; color: #6b7280; font-size: 13px; }
        .stat-card h3 { margin: 4px 0 0; font-weight: 700; color: #111827; }

        /* Table card */
        .table-card {
            background: #fff; border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            overflow: hidden;
            animation: fadeUp 0.5s ease 0.2s both;
        }
        .table-card .card-header {
            background: #fff; padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;
        }
        .table-card .card-header h5 { margin: 0; font-weight: 700; color: #1e3a8a; }

        /* Search */
        .search-box { position: relative; }
        .search-box input {
            padding-left: 38px; border-radius: 10px;
            border: 1px solid #e2e8f0; background: #f8fafc;
            font-size: 14px; width: 220px; transition: 0.3s;
        }
        .search-box input:focus { width: 260px; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }

        /* Table */
        .table thead th {
            background: #f8fafc; color: #374151;
            font-size: 12px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.5px; border: none; padding: 14px 16px;
        }
        .table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            animation: fadeIn 0.4s ease both;
            transition: background 0.2s;
        }
        .table tbody tr:hover { background: #f0f7ff; }
        .table tbody td { padding: 14px 16px; vertical-align: middle; font-size: 14px; color: #374151; border: none; }

        /* Badge qty */
        .qty-badge {
            display: inline-block; padding: 4px 12px;
            border-radius: 20px; font-size: 12px; font-weight: 600;
        }
        .qty-low { background: #fef2f2; color: #dc2626; }
        .qty-ok  { background: #f0fdf4; color: #16a34a; }

        /* Action buttons */
        .btn-edit {
            background: #eff6ff; color: #2563eb; border: none;
            padding: 6px 14px; border-radius: 8px; font-size: 13px;
            transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
        }
        .btn-edit:hover { background: #2563eb; color: #fff; transform: translateY(-1px); }
        .btn-del {
            background: #fef2f2; color: #dc2626; border: none;
            padding: 6px 14px; border-radius: 8px; font-size: 13px;
            transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
        }
        .btn-del:hover { background: #dc2626; color: #fff; transform: translateY(-1px); }

        /* Empty state */
        .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
        .empty-state i { font-size: 56px; margin-bottom: 16px; display: block; }

        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Toast */
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; }

        @media (max-width: 768px) {
            .sidebar { width: 0; overflow: hidden; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="brand"><i class="fa-solid fa-pills"></i> PharmaCare</div>
    <a href="../dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="add-medicine.php"><i class="fa-solid fa-plus-circle"></i> Add Medicine</a>
    <a href="manage-medicine.php" class="active"><i class="fa-solid fa-capsules"></i> Manage Medicines</a>
    <a href="../suppliers/manage-supplier.php"><i class="fa-solid fa-truck"></i> Suppliers</a>
    <a href="../reports/sales-report.php"><i class="fa-solid fa-chart-bar"></i> Reports</a>
    <a href="../logout.php" style="margin-top:auto; color:#fca5a5;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- Main -->
<div class="main-content">

    <!-- Topbar -->
    <div class="topbar">
        <h5><i class="fa-solid fa-capsules me-2 text-primary"></i>Manage Medicines</h5>
        <a href="add-medicine.php" class="btn btn-primary btn-sm rounded-pill px-4">
            <i class="fa-solid fa-plus me-1"></i> Add New
        </a>
    </div>

    <div class="page-body">

        <?php if (isset($_GET['msg'])): ?>
        <div class="toast-container">
            <div id="liveToast" class="toast align-items-center text-white <?= $_GET['msg'] === 'deleted' ? 'bg-danger' : 'bg-success' ?> border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa-solid <?= $_GET['msg'] === 'deleted' ? 'fa-trash' : 'fa-check' ?> me-2"></i>
                        <?= $_GET['msg'] === 'deleted' ? 'Medicine deleted successfully.' : 'Action completed.' ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php
        // Stats
        $total   = mysqli_num_rows($result);
        $low_res = mysqli_query($conn, "SELECT COUNT(*) as c FROM medicines WHERE quantity < 10");
        $low_row = mysqli_fetch_assoc($low_res);
        $low     = $low_row['c'];
        $val_res = mysqli_query($conn, "SELECT SUM(price * quantity) as total FROM medicines");
        $val_row = mysqli_fetch_assoc($val_res);
        $value   = number_format($val_row['total'] ?? 0, 2);
        mysqli_data_seek($result, 0); // reset pointer
        ?>

        <!-- Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-4" style="animation-delay:0s">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#2563eb,#60a5fa)">
                        <i class="fa-solid fa-pills"></i>
                    </div>
                    <div>
                        <h6>Total Medicines</h6>
                        <h3><?= $total ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4" style="animation-delay:0.1s">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#dc2626,#f87171)">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h6>Low Stock (&lt;10)</h6>
                        <h3><?= $low ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4" style="animation-delay:0.2s">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#16a34a,#4ade80)">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                    <div>
                        <h6>Inventory Value</h6>
                        <h3>$<?= $value ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-list me-2"></i>Medicine List</h5>
                <div class="search-box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search medicines..." class="form-control form-control-sm">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" id="medicineTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Medicine Name</th>
                            <th>Category</th>
                            <th>Batch No</th>
                            <th>Expiry Date</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 1;
                    $has_rows = false;
                    while ($row = mysqli_fetch_assoc($result)):
                        $has_rows = true;
                        $qty_class = $row['quantity'] < 10 ? 'qty-low' : 'qty-ok';
                        $expiry_ts = strtotime($row['expiry_date'] ?? '');
                        $expiry_str = $expiry_ts ? date('d M Y', $expiry_ts) : 'N/A';
                        $expiry_warn = $expiry_ts && $expiry_ts < strtotime('+30 days') ? 'text-danger fw-semibold' : '';
                    ?>
                    <tr style="animation-delay: <?= $i * 0.05 ?>s">
                        <td><span class="text-muted"><?= $i++ ?></span></td>
                        <td><strong><?= htmlspecialchars($row['medicine_name']) ?></strong></td>
                        <td><?= htmlspecialchars($row['category'] ?? '—') ?></td>
                        <td><code><?= htmlspecialchars($row['batch_no'] ?? '—') ?></code></td>
                        <td class="<?= $expiry_warn ?>"><?= $expiry_str ?></td>
                        <td><span class="qty-badge <?= $qty_class ?>"><?= $row['quantity'] ?></span></td>
                        <td><strong>$<?= number_format($row['price'], 2) ?></strong></td>
                        <td>
                            <a href="edit-medicine.php?id=<?= $row['id'] ?>" class="btn-edit">
                                <i class="fa-solid fa-pen"></i> Edit
                            </a>
                            <a href="manage-medicine.php?delete=<?= $row['id'] ?>" class="btn-del ms-1"
                               onclick="return confirm('Delete this medicine?')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if (!$has_rows): ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fa-solid fa-box-open"></i>
                                <p class="fw-semibold">No medicines found</p>
                                <a href="add-medicine.php" class="btn btn-primary btn-sm rounded-pill px-4">Add First Medicine</a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /page-body -->
</div><!-- /main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Live search
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#medicineTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    // Auto-dismiss toast
    const toastEl = document.getElementById('liveToast');
    if (toastEl) setTimeout(() => bootstrap.Toast.getOrCreateInstance(toastEl).hide(), 3000);
</script>
</body>
</html>
