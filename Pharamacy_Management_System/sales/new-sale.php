<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit(); }
include("../includes/db.php");

$success = '';
if (isset($_POST['checkout'])) {
    $customer = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $payment  = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $total    = floatval($_POST['total_amount']);
    $meds     = $_POST['med_id']  ?? [];
    $qtys     = $_POST['med_qty'] ?? [];
    $prices   = $_POST['med_price'] ?? [];

    if (!empty($meds)) {
        mysqli_query($conn, "INSERT INTO sales (customer_name,total_amount,payment_method) VALUES ('$customer',$total,'$payment')");
        $sale_id = mysqli_insert_id($conn);

        foreach ($meds as $k => $mid) {
            $mid   = intval($mid);
            $qty   = intval($qtys[$k]);
            $price = floatval($prices[$k]);
            mysqli_query($conn, "INSERT INTO sale_items (sale_id,medicine_id,quantity,price) VALUES ($sale_id,$mid,$qty,$price)");
            mysqli_query($conn, "UPDATE medicines SET quantity = quantity - $qty WHERE id=$mid");
            mysqli_query($conn, "INSERT INTO stock_log (medicine_id,action,quantity) VALUES ($mid,'sale',$qty)");
        }
        $success = "Sale #$sale_id recorded successfully!";
    }
}

$medicines = mysqli_query($conn, "SELECT id, medicine_name, price, quantity FROM medicines WHERE quantity > 0 ORDER BY medicine_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Sale | PharmaCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .cart-item { background:#f8fafc; border-radius:10px; padding:12px 16px; margin-bottom:8px; animation:fadeIn 0.3s ease; }
        .cart-item .remove-btn { background:none; border:none; color:#dc2626; cursor:pointer; font-size:16px; }
        #totalDisplay { font-size:28px; font-weight:800; color:#16a34a; }
    </style>
</head>
<body>
<?php include("../includes/sidebar.php"); ?>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fa-solid fa-cart-plus me-2 text-primary"></i>New Sale</h5>
        <a href="sales-history.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-history me-1"></i> Sales History
        </a>
    </div>

    <div class="page-body">

        <?php if ($success): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
            <i class="fa-solid fa-circle-check"></i> <?= $success ?>
            <a href="sales-history.php" class="ms-auto btn btn-sm btn-success rounded-pill px-3">View History</a>
        </div>
        <?php endif; ?>

        <form method="POST" id="saleForm">
        <div class="row g-3">

            <!-- Left: Add Items -->
            <div class="col-lg-7">
                <div class="form-card mb-3">
                    <h6 class="fw-bold mb-3" style="color:#1e3a8a"><i class="fa-solid fa-pills me-2"></i>Add Medicine to Cart</h6>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Select Medicine</label>
                            <select id="medSelect" class="form-select">
                                <option value="">-- Choose Medicine --</option>
                                <?php while ($m = mysqli_fetch_assoc($medicines)): ?>
                                <option value="<?= $m['id'] ?>"
                                        data-price="<?= $m['price'] ?>"
                                        data-name="<?= htmlspecialchars($m['medicine_name']) ?>"
                                        data-stock="<?= $m['quantity'] ?>">
                                    <?= htmlspecialchars($m['medicine_name']) ?> (Stock: <?= $m['quantity'] ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" id="medQty" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Price</label>
                            <input type="text" id="medPrice" class="form-control" readonly placeholder="0.00">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary w-100 rounded-3" onclick="addToCart()">
                                <i class="fa-solid fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cart -->
                <div class="form-card">
                    <h6 class="fw-bold mb-3" style="color:#1e3a8a"><i class="fa-solid fa-cart-shopping me-2"></i>Cart Items</h6>
                    <div id="cartItems">
                        <div class="empty-state" id="emptyCart" style="padding:30px">
                            <i class="fa-solid fa-cart-shopping" style="font-size:36px"></i>
                            <p class="mt-2">No items added yet</p>
                        </div>
                    </div>
                    <div id="cartInputs"></div>
                </div>
            </div>

            <!-- Right: Checkout -->
            <div class="col-lg-5">
                <div class="form-card">
                    <h6 class="fw-bold mb-3" style="color:#1e3a8a"><i class="fa-solid fa-receipt me-2"></i>Checkout</h6>

                    <div class="mb-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" placeholder="Walk-in Customer" value="Walk-in Customer">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option>Cash</option>
                            <option>Card</option>
                            <option>Mobile Payment</option>
                            <option>Insurance</option>
                        </select>
                    </div>

                    <div class="bg-light rounded-3 p-3 mb-3 text-center">
                        <div class="text-muted mb-1" style="font-size:13px">Total Amount</div>
                        <div id="totalDisplay">$0.00</div>
                    </div>

                    <input type="hidden" name="total_amount" id="totalInput" value="0">

                    <button type="submit" name="checkout" class="btn btn-success w-100 rounded-pill py-3 fw-bold" id="checkoutBtn" disabled>
                        <i class="fa-solid fa-check me-2"></i> Complete Sale
                    </button>
                </div>
            </div>

        </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let cart = [];

document.getElementById('medSelect').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    document.getElementById('medPrice').value = opt.dataset.price || '';
});

function addToCart() {
    const sel   = document.getElementById('medSelect');
    const opt   = sel.options[sel.selectedIndex];
    const qty   = parseInt(document.getElementById('medQty').value);
    const id    = sel.value;
    const name  = opt.dataset.name;
    const price = parseFloat(opt.dataset.price);
    const stock = parseInt(opt.dataset.stock);

    if (!id) return alert('Please select a medicine.');
    if (qty < 1 || qty > stock) return alert(`Invalid quantity. Available stock: ${stock}`);

    const existing = cart.find(i => i.id === id);
    if (existing) {
        if (existing.qty + qty > stock) return alert(`Total quantity exceeds stock (${stock}).`);
        existing.qty += qty;
    } else {
        cart.push({ id, name, price, qty });
    }
    renderCart();
}

function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const inputs    = document.getElementById('cartInputs');
    const empty     = document.getElementById('emptyCart');
    let total = 0;

    if (cart.length === 0) {
        container.innerHTML = '';
        container.appendChild(empty);
        empty.style.display = 'block';
        inputs.innerHTML = '';
        document.getElementById('totalDisplay').textContent = '$0.00';
        document.getElementById('totalInput').value = '0';
        document.getElementById('checkoutBtn').disabled = true;
        return;
    }

    empty.style.display = 'none';
    container.innerHTML = '';
    inputs.innerHTML = '';

    cart.forEach(item => {
        const subtotal = item.price * item.qty;
        total += subtotal;

        container.innerHTML += `
        <div class="cart-item d-flex justify-content-between align-items-center">
            <div>
                <strong>${item.name}</strong>
                <div class="text-muted" style="font-size:12px">$${item.price.toFixed(2)} × ${item.qty} = <strong>$${subtotal.toFixed(2)}</strong></div>
            </div>
            <button type="button" class="remove-btn" onclick="removeFromCart('${item.id}')">
                <i class="fa-solid fa-times-circle"></i>
            </button>
        </div>`;

        inputs.innerHTML += `
        <input type="hidden" name="med_id[]"    value="${item.id}">
        <input type="hidden" name="med_qty[]"   value="${item.qty}">
        <input type="hidden" name="med_price[]" value="${item.price}">`;
    });

    document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);
    document.getElementById('totalInput').value = total.toFixed(2);
    document.getElementById('checkoutBtn').disabled = false;
}
</script>
</body>
</html>
