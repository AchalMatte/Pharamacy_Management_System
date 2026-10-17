<?php
session_start();
include("includes/db.php");

$error = '';
if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $result   = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$password'");
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['admin']    = $user['email'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = 'Invalid email or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | PharmaCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-wrapper {
            display: flex; width: 900px; max-width: 95vw;
            border-radius: 24px; overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.6s ease;
        }
        .login-left {
            flex: 1; background: linear-gradient(160deg, #1e3a8a, #2563eb);
            padding: 60px 40px; color: #fff;
            display: flex; flex-direction: column; justify-content: center;
        }
        .login-left .pill-icon { font-size: 80px; margin-bottom: 24px; opacity: 0.9; }
        .login-left h2 { font-weight: 800; font-size: 28px; margin-bottom: 12px; }
        .login-left p  { opacity: 0.75; font-size: 15px; line-height: 1.7; }
        .login-left .feature { display: flex; align-items: center; gap: 10px; margin-top: 18px; font-size: 14px; opacity: 0.85; }
        .login-left .feature i { width: 28px; height: 28px; background: rgba(255,255,255,0.15); border-radius: 8px; display: flex; align-items: center; justify-content: center; }

        .login-right {
            width: 420px; background: #fff;
            padding: 56px 48px;
            display: flex; flex-direction: column; justify-content: center;
        }
        .login-right h3 { font-weight: 800; color: #1e3a8a; margin-bottom: 6px; }
        .login-right p.sub { color: #6b7280; font-size: 14px; margin-bottom: 32px; }

        .form-floating label { color: #94a3b8; font-size: 14px; }
        .form-floating .form-control {
            border-radius: 12px; border: 1.5px solid #e2e8f0;
            font-size: 14px; height: 52px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-floating .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .btn-login {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #fff; border: none; border-radius: 12px;
            padding: 14px; font-size: 15px; font-weight: 600;
            width: 100%; transition: all 0.3s; margin-top: 8px;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(37,99,235,0.35); }
        .btn-login:active { transform: translateY(0); }

        .error-alert {
            background: #fef2f2; border: 1px solid #fecaca;
            color: #dc2626; border-radius: 10px;
            padding: 12px 16px; font-size: 13px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
            animation: shake 0.4s ease;
        }
        .pass-toggle { position: relative; }
        .pass-toggle .toggle-btn {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #94a3b8; cursor: pointer; z-index: 5;
        }
        .back-link { text-align: center; margin-top: 20px; font-size: 13px; color: #6b7280; }
        .back-link a { color: #2563eb; text-decoration: none; font-weight: 600; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            25%      { transform: translateX(-6px); }
            75%      { transform: translateX(6px); }
        }
        @media (max-width: 700px) {
            .login-left { display: none; }
            .login-right { width: 100%; padding: 40px 28px; }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <!-- Left Panel -->
    <div class="login-left">
        <div class="pill-icon"><i class="fa-solid fa-pills"></i></div>
        <h2>PharmaCare System</h2>
        <p>Your complete pharmacy management solution for inventory, billing, and reporting.</p>
        <div class="feature"><i class="fa-solid fa-box"></i> Smart Inventory Management</div>
        <div class="feature"><i class="fa-solid fa-receipt"></i> Fast POS Billing</div>
        <div class="feature"><i class="fa-solid fa-chart-bar"></i> Real-time Reports</div>
        <div class="feature"><i class="fa-solid fa-truck"></i> Supplier Tracking</div>
    </div>

    <!-- Right Panel -->
    <div class="login-right">
        <h3>Welcome Back</h3>
        <p class="sub">Sign in to your pharmacy dashboard</p>

        <?php if ($error): ?>
        <div class="error-alert"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control" id="emailInput"
                       placeholder="admin@gmail.com" required
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                <label for="emailInput"><i class="fa-solid fa-envelope me-1"></i> Email Address</label>
            </div>

            <div class="pass-toggle mb-3">
                <div class="form-floating">
                    <input type="password" name="password" class="form-control" id="passInput"
                           placeholder="Password" required>
                    <label for="passInput"><i class="fa-solid fa-lock me-1"></i> Password</label>
                </div>
                <button type="button" class="toggle-btn" onclick="togglePass()">
                    <i class="fa-solid fa-eye" id="eyeIcon"></i>
                </button>
            </div>

            <button type="submit" name="login" class="btn-login">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In
            </button>
        </form>

        <div class="back-link">
            <a href="index.php"><i class="fa-solid fa-arrow-left me-1"></i> Back to Home</a>
        </div>
    </div>
</div>

<script>
function togglePass() {
    const input = document.getElementById('passInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye';
    }
}
</script>
</body>
</html>
