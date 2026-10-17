<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit(); }
include("../includes/db.php");

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: manage-medicine.php"); exit(); }

$med = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM medicines WHERE id=$id"));
if (!$med) { header("Location: manage-medicine.php"); exit(); }

$success = $error = '';
if (isset($_POST['update'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $batch    = mysqli_real_escape_string($conn, $_POST['batch']);
    $expiry   = mysqli_real_escape_string($conn, $_POST['expiry']);
    $qty      = intval($_POST['quantity']);
    $price    = floatval($_POST['price']);
    $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);

    $q = "UPDATE medicines SET
            medicine_name='$name', category='$category', batch_no='$batch',
            expiry_date='$expiry', quantity=$qty, price=$price, supplier='$supplier'
          WHERE id=$id";

    if (mysqli_query($conn, $q)) {
        $success = 'Medicine updated successfully!';
        $med = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM medicines WHERE id=$id"));
    } else {
        $error = 'Update failed. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Medicine | PharmaCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fa-solid fa-pen me-2 text-primary"></i>Edit Medicine</h5>
        <a href="manage-medicine.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="page-body">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <?php if ($success): ?>
                <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-circle-check"></i> <?= $success ?>
                </div>
                <?php endif; ?>
                <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
                </div>
                <?php endif; ?>

                <div class="form-card">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#2563eb,#60a5fa);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px">
                            <i class="fa-solid fa-capsules"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-700" style="color:#1e3a8a">Edit Medicine</h5>
                            <small class="text-muted">ID #<?= $id ?> — <?= htmlspecialchars($med['medicine_name']) ?></small>
                        </div>
                    </div>

                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       value="<?= htmlspecialchars($med['medicine_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <input type="text" name="category" class="form-control"
                                       value="<?= htmlspecialchars($med['category'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Batch No</label>
                                <input type="text" name="batch" class="form-control"
                                       value="<?= htmlspecialchars($med['batch_no'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" name="expiry" class="form-control"
                                       value="<?= $med['expiry_date'] ?? '' ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" class="form-control" min="0"
                                       value="<?= $med['quantity'] ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price ($) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0"
                                       value="<?= $med['price'] ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Supplier</label>
                                <input type="text" name="supplier" class="form-control"
                                       value="<?= htmlspecialchars($med['supplier'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" name="update" class="btn btn-primary rounded-pill px-5">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
                            </button>
                            <a href="manage-medicine.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
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
