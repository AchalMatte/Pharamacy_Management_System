<?php
// Detect current page for active link
$current = basename($_SERVER['PHP_SELF']);
$dir     = basename(dirname($_SERVER['PHP_SELF']));

function isActive($pages) {
    global $current, $dir;
    foreach ((array)$pages as $p) {
        if (strpos($p, '/') !== false) {
            [$d, $f] = explode('/', $p);
            if ($dir === $d && $current === $f) return 'active';
        } else {
            if ($current === $p) return 'active';
        }
    }
    return '';
}

// Resolve root path for links
$root = (dirname($_SERVER['PHP_SELF']) === '/' || dirname($_SERVER['PHP_SELF']) === '\\') ? '' : '../';
if ($current === 'dashboard.php') $root = '';
?>
<style>
.sidebar {
    width: 240px; min-height: 100vh;
    background: linear-gradient(180deg, #1e3a8a 0%, #2563eb 100%);
    padding: 0; position: fixed; top: 0; left: 0;
    box-shadow: 4px 0 20px rgba(0,0,0,0.15); z-index: 200;
    display: flex; flex-direction: column;
}
.sidebar .brand {
    color: #fff; font-size: 18px; font-weight: 700;
    padding: 22px 24px; border-bottom: 1px solid rgba(255,255,255,0.12);
    display: flex; align-items: center; gap: 10px; text-decoration: none;
}
.sidebar .nav-label {
    color: rgba(255,255,255,0.4); font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1px;
    padding: 18px 24px 6px;
}
.sidebar a.nav-link {
    display: flex; align-items: center; gap: 12px;
    color: rgba(255,255,255,0.75); text-decoration: none;
    padding: 11px 24px; font-size: 14px; transition: all 0.2s;
    border-left: 3px solid transparent;
}
.sidebar a.nav-link:hover { background: rgba(255,255,255,0.1); color: #fff; padding-left: 28px; }
.sidebar a.nav-link.active { background: rgba(255,255,255,0.15); color: #fff; border-left-color: #fff; }
.sidebar a.nav-link i { width: 18px; text-align: center; font-size: 15px; }
.sidebar .logout-link { margin-top: auto; border-top: 1px solid rgba(255,255,255,0.12); }
.sidebar .logout-link a { color: #fca5a5 !important; }
.sidebar .logout-link a:hover { background: rgba(220,38,38,0.2) !important; color: #fff !important; }
</style>

<div class="sidebar">
    <a href="<?= $root ?>dashboard.php" class="brand">
        <i class="fa-solid fa-pills"></i> PharmaCare
    </a>

    <div class="nav-label">Main</div>
    <a href="<?= $root ?>dashboard.php" class="nav-link <?= isActive('dashboard.php') ?>">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>

    <div class="nav-label">Medicines</div>
    <a href="<?= $root ?>medicines/add-medicine.php" class="nav-link <?= isActive('medicines/add-medicine.php') ?>">
        <i class="fa-solid fa-plus-circle"></i> Add Medicine
    </a>
    <a href="<?= $root ?>medicines/manage-medicine.php" class="nav-link <?= isActive('medicines/manage-medicine.php') ?>">
        <i class="fa-solid fa-capsules"></i> Manage Medicines
    </a>

    <div class="nav-label">Sales</div>
    <a href="<?= $root ?>sales/new-sale.php" class="nav-link <?= isActive('sales/new-sale.php') ?>">
        <i class="fa-solid fa-cart-plus"></i> New Sale
    </a>
    <a href="<?= $root ?>sales/sales-history.php" class="nav-link <?= isActive('sales/sales-history.php') ?>">
        <i class="fa-solid fa-receipt"></i> Sales History
    </a>

    <div class="nav-label">Other</div>
    <a href="<?= $root ?>suppliers/manage-supplier.php" class="nav-link <?= isActive('suppliers/manage-supplier.php') ?>">
        <i class="fa-solid fa-truck"></i> Suppliers
    </a>
    <a href="<?= $root ?>reports/sales-report.php" class="nav-link <?= isActive(['reports/sales-report.php','reports/stock-report.php']) ?>">
        <i class="fa-solid fa-chart-bar"></i> Reports
    </a>

    <div class="logout-link">
        <a href="<?= $root ?>logout.php" class="nav-link">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</div>
