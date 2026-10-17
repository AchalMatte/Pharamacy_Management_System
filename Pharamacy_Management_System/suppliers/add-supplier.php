<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit(); }
include("../includes/db.php");

$success = $error = '';
if (isset($_POST['add'])) {
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $q = "INSERT INTO suppliers (name,phone,email,address) VALUES ('$name','$phone','$email','$address')";
    if (mysqli_query($conn, $q)) {
        $success = "Supplier <strong>$name</strong> added successfully!";
    } else {
        $error = 'Failed to add supplier.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier | PharmaCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fa-solid fa-truck me-2 text-primary"></i>Add Supplier</h5>
        <a href="manage-supplier.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-list me-1"></i> View All
        </a>
    </div>

    <div class="page-body">
        <div class="row justify-content-center">
            <div class="col-lg-7">

                <?php if ($success): ?>
                <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
                    <i class="fa-solid fa-circle-check"></i> <?= $success ?>
                    <a href="manage-supplier.php" class="ms-auto btn btn-sm btn-success rounded-pill px-3">View List</a>
                </div>
                <?php endif; ?>
                <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
                </div>
                <?php endif; ?>

                <div class="form-card">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#0891b2,#67e8f9);border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px">
                            <i class="fa-solid fa-truck"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" style="color:#1e3a8a">New Supplier</h5>
                            <small class="text-muted">Add a new medicine supplier</small>
                        </div>
                    </div>

                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Company name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="+1 234 567 8900">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="supplier@email.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" placeholder="City, Country">
                            </div>
                        </div>
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" name="add" class="btn btn-primary rounded-pill px-5">
                                <i class="fa-solid fa-plus me-2"></i> Add Supplier
                            </button>
                            <button type="reset" class="btn btn-outline-secondary rounded-pill px-4">Reset</button>
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
