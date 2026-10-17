<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit(); }
include("../includes/db.php");

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM suppliers WHERE id=$id");
    header("Location: manage-supplier.php?msg=deleted");
    exit();
}

$suppliers = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY id DESC");
$total     = mysqli_num_rows($suppliers);
mysqli_data_seek($suppliers, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Suppliers | PharmaCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fa-solid fa-truck me-2 text-primary"></i>Manage Suppliers</h5>
        <a href="add-supplier.php" class="btn btn-primary btn-sm rounded-pill px-4">
            <i class="fa-solid fa-plus me-1"></i> Add Supplier
        </a>
    </div>

    <div class="page-body">

        <?php if (isset($_GET['msg'])): ?>
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999">
            <div id="liveToast" class="toast align-items-center text-white bg-danger border-0 show">
                <div class="d-flex">
                    <div class="toast-body"><i class="fa-solid fa-trash me-2"></i>Supplier deleted.</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Stat -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:linear-gradient(135deg,#0891b2,#67e8f9)">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <div><h6>Total Suppliers</h6><h3><?= $total ?></h3></div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="card-header">
                <h5><i class="fa-solid fa-list me-2"></i>Supplier List</h5>
                <div class="search-box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search suppliers..." class="form-control form-control-sm">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" id="supplierTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 1; $has = false;
                    while ($row = mysqli_fetch_assoc($suppliers)):
                        $has = true;
                    ?>
                    <tr style="animation-delay:<?= $i * 0.05 ?>s">
                        <td class="text-muted"><?= $i++ ?></td>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td><?= htmlspecialchars($row['phone'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '—') ?></td>
                        <td class="text-muted"><?= htmlspecialchars($row['address'] ?? '—') ?></td>
                        <td class="text-muted" style="font-size:12px"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="manage-supplier.php?delete=<?= $row['id'] ?>" class="btn-del"
                               onclick="return confirm('Delete this supplier?')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if (!$has): ?>
                    <tr><td colspan="7">
                        <div class="empty-state">
                            <i class="fa-solid fa-truck"></i>
                            <p class="fw-semibold">No suppliers found</p>
                            <a href="add-supplier.php" class="btn btn-primary btn-sm rounded-pill px-4">Add First Supplier</a>
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
    document.querySelectorAll('#supplierTable tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
const t = document.getElementById('liveToast');
if (t) setTimeout(() => bootstrap.Toast.getOrCreateInstance(t).hide(), 3000);
</script>
</body>
</html>
