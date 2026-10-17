<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit(); }
include("../includes/db.php");

$success = $error = '';
if (isset($_POST['add'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $batch    = mysqli_real_escape_string($conn, $_POST['batch']);
    $expiry   = mysqli_real_escape_string($conn, $_POST['expiry']);
    $qty      = intval($_POST['quantity']);
    $price    = floatval($_POST['price']);
    $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);

    $q = "INSERT INTO medicines (medicine_name,category,batch_no,expiry_date,quantity,price,supplier)
          VALUES ('$name','$category','$batch','$expiry',$qty,$price,'$supplier')";

    if (mysqli_query($conn, $q)) {
        $success = "Medicine <strong>$name</strong> added successfully!";
    } else {
        $error = 'Failed to add medicine. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medicine | PharmaCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fa-solid fa-plus-circle me-2 text-primary"></i>Add Medicine</h5>
        <a href="manage-medicine.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-list me-1"></i> View All
        </a>
    </div>

    <div class="page-body">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <?php if ($success): ?>
                <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
                    <i class="fa-solid fa-circle-check"></i> <?= $success ?>
                    <a href="manage-medicine.php" class="ms-auto btn btn-sm btn-success rounded-pill px-3">View List</a>
                </div>
                <?php endif; ?>
                <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
                </div>
                <?php endif; ?>

                <div class="form-card">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#2563eb,#60a5fa);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" style="color:#1e3a8a">New Medicine</h5>
                            <small class="text-muted">Fill in the details below to add to inventory</small>
                        </div>
                    </div>

                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Paracetamol 500mg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <input type="text" name="category" class="form-control" placeholder="e.g. Analgesic">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Batch No</label>
                                <input type="text" name="batch" class="form-control" placeholder="e.g. BT-2024-001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" name="expiry" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" min="0" placeholder="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price ($) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Supplier</label>
                                <input type="text" name="supplier" class="form-control" placeholder="Supplier name">
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" name="add" class="btn btn-primary rounded-pill px-5">
                                <i class="fa-solid fa-plus me-2"></i> Add Medicine
                            </button>
                            <button type="reset" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
