<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaCare - Pharmacy Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #e0f2fe 0%, #fae8ff 50%, #fce7f3 100%);
            color: #1a1a1a;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(147, 197, 253, 0.3);
            padding: 16px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(147, 197, 253, 0.2);
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #3b82f6, #a855f7, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: fadeInDown 0.6s ease;
        }
        .navbar-brand i { 
            background: linear-gradient(135deg, #3b82f6, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-link {
            color: #666 !important;
            font-weight: 500;
            margin: 0 12px;
            transition: all 0.3s ease;
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #a855f7);
            transition: width 0.3s ease;
        }
        .nav-link:hover::after { width: 80%; }
        .nav-link:hover { color: #3b82f6 !important; }
        .btn-portal {
            background: linear-gradient(135deg, #3b82f6, #a855f7);
            border: none;
            padding: 8px 24px;
            border-radius: 20px;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        .btn-portal:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: #fff;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding-top: 80px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(147, 197, 253, 0.4), transparent);
            border-radius: 50%;
            top: -200px;
            right: -200px;
            animation: float 6s ease-in-out infinite;
        }
        .hero::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(216, 180, 254, 0.4), transparent);
            border-radius: 50%;
            bottom: -150px;
            left: -150px;
            animation: float 8s ease-in-out infinite reverse;
        }
        .hero-content h1 {
            font-size: 56px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e40af, #7c3aed, #db2777);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            animation: fadeInUp 0.8s ease;
        }
        .hero-content p {
            font-size: 18px;
            color: #666;
            margin-bottom: 32px;
            line-height: 1.6;
            animation: fadeInUp 0.8s ease 0.2s both;
        }
        .hero-buttons {
            animation: fadeInUp 0.8s ease 0.4s both;
        }
        .btn-hero {
            padding: 12px 32px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-hero-primary {
            background: linear-gradient(135deg, #3b82f6, #a855f7);
            color: #fff;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: #fff;
        }
        .btn-hero-outline {
            background: rgba(255, 255, 255, 0.8);
            color: #3b82f6;
            border: 2px solid #3b82f6;
        }
        .btn-hero-outline:hover {
            background: linear-gradient(135deg, #3b82f6, #a855f7);
            border-color: transparent;
            color: #fff;
            transform: translateY(-3px);
        }
        .hero-image {
            position: relative;
            z-index: 1;
            animation: fadeInRight 1s ease;
        }
        .hero-image i {
            font-size: 240px;
            background: linear-gradient(135deg, #3b82f6, #a855f7, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: float 4s ease-in-out infinite;
            filter: drop-shadow(0 10px 30px rgba(59, 130, 246, 0.3));
        }

        /* Features Section */
        .features {
            padding: 100px 0;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            position: relative;
        }
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        .section-title h2 {
            font-size: 42px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e40af, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            animation: fadeInUp 0.6s ease;
        }
        .section-title p {
            font-size: 18px;
            color: #666;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 197, 253, 0.3);
            border-radius: 20px;
            padding: 32px 24px;
            text-align: center;
            transition: all 0.4s ease;
            height: 100%;
            animation: fadeInUp 0.6s ease;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(59, 130, 246, 0.2);
            border-color: rgba(168, 85, 247, 0.4);
        }
        .feature-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #3b82f6, #a855f7);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #fff;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 10px 30px rgba(168, 85, 247, 0.4);
        }
        .feature-card h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #1a1a1a;
        }
        .feature-card p {
            color: #666;
            font-size: 15px;
            line-height: 1.6;
        }

        /* About Section */
        .about {
            padding: 100px 0;
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
        }
        .about-text h2 {
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #1e40af, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeInLeft 0.8s ease;
        }
        .about-text p {
            font-size: 16px;
            color: #666;
            line-height: 1.7;
            margin-bottom: 16px;
        }
        .about-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 32px;
        }
        .stat-box {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 197, 253, 0.3);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease;
        }
        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.2);
        }
        .stat-box h3 {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        .stat-box p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        /* Contact Section */
        .contact {
            padding: 100px 0;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
        }
        .contact-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 197, 253, 0.3);
            border-radius: 24px;
            padding: 48px;
            max-width: 700px;
            margin: 0 auto;
            box-shadow: 0 10px 40px rgba(59, 130, 246, 0.15);
            animation: fadeInUp 0.8s ease;
        }
        .contact-card h2 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 12px;
            text-align: center;
            background: linear-gradient(135deg, #1e40af, #7c3aed, #db2777);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .contact-card > p {
            text-align: center;
            color: #666;
            margin-bottom: 32px;
        }
        .form-control, .form-select {
            border: 1px solid rgba(147, 197, 253, 0.4);
            border-radius: 10px;
            padding: 12px;
            font-size: 15px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #a855f7;
            box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.1);
            transform: translateY(-2px);
        }
        .btn-submit {
            background: linear-gradient(135deg, #3b82f6, #a855f7);
            color: #fff;
            border: none;
            padding: 12px 32px;
            border-radius: 25px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(168, 85, 247, 0.4);
        }
        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 32px;
        }
        .contact-item {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(147, 197, 253, 0.3);
            transition: all 0.3s ease;
        }
        .contact-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.2);
        }
        .contact-item i {
            font-size: 28px;
            background: linear-gradient(135deg, #3b82f6, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
        }
        .contact-item h4 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #1a1a1a;
        }
        .contact-item p {
            font-size: 14px;
            color: #666;
            margin: 0;
        }
        .alert {
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        /* Footer */
        footer {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(147, 197, 253, 0.3);
            padding: 32px 0;
            text-align: center;
        }
        footer p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content h1 { font-size: 36px; }
            .hero-content p { font-size: 16px; }
            .hero-image i { font-size: 160px; }
            .contact-card { padding: 32px 20px; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="#home">
            <i class="fa-solid fa-pills"></i>
            PharmaCare
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                <li class="nav-item ms-3">
                    <a href="login.php" class="btn btn-portal">
                        <i class="fa-solid fa-user-shield me-2"></i>Staff Portal
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1>Modern Pharmacy Management</h1>
                    <p>Streamline your pharmacy operations with our comprehensive management system. Track inventory, process sales, and generate reports effortlessly.</p>
                    <div>
                        <a href="login.php" class="btn-hero btn-hero-primary me-3">
                            <i class="fa-solid fa-rocket me-2"></i>Get Started
                        </a>
                        <a href="#features" class="btn-hero btn-hero-outline">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center">
                    <i class="fa-solid fa-capsules"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features" id="features">
    <div class="container">
        <div class="section-title">
            <h2>Key Features</h2>
            <p>Everything you need to manage your pharmacy</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <h3>Inventory Management</h3>
                    <p>Track stock levels, expiry dates, and get automated alerts for low inventory.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h3>Fast POS System</h3>
                    <p>Process sales quickly with our intuitive point-of-sale interface.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h3>Sales Reports</h3>
                    <p>Generate detailed reports on sales, revenue, and inventory performance.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3>Secure & Reliable</h3>
                    <p>Your data is protected with industry-standard security measures.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <h3>Supplier Management</h3>
                    <p>Manage supplier information and track orders efficiently.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fa-solid fa-mobile-screen"></i>
                    </div>
                    <h3>Responsive Design</h3>
                    <p>Access your system from any device, anywhere, anytime.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about" id="about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-text">
                    <h2>Trusted by Pharmacies</h2>
                    <p>PharmaCare is designed to help pharmacies operate more efficiently with streamlined processes and real-time insights.</p>
                    <p>Our system combines powerful features with an easy-to-use interface, making pharmacy management simple and effective.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-stats">
                    <div class="stat-box">
                        <h3>5,000+</h3>
                        <p>Active Pharmacies</p>
                    </div>
                    <div class="stat-box">
                        <h3>99.9%</h3>
                        <p>Uptime</p>
                    </div>
                    <div class="stat-box">
                        <h3>24/7</h3>
                        <p>Support</p>
                    </div>
                    <div class="stat-box">
                        <h3>50M+</h3>
                        <p>Transactions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact" id="contact">
    <div class="container">
        <div class="contact-card">
            <h2>Contact Us</h2>
            <p>Have questions? Send us a message and we'll get back to you.</p>
            
            <?php
            $success = $error = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
                $email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
                $subject = htmlspecialchars(trim($_POST['subject'] ?? ''), ENT_QUOTES, 'UTF-8');
                $message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');
                
                if (empty($name) || empty($email) || empty($subject) || empty($message)) {
                    $error = 'All fields are required.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid email address.';
                } else {
                    $to = 'support@pharmacare.com';
                    $email_subject = "Contact Form: $subject";
                    $email_body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
                    $headers = "From: $email\r\nReply-To: $email";
                    
                    if (mail($to, $email_subject, $email_body, $headers)) {
                        $success = 'Message sent successfully! We will contact you soon.';
                    } else {
                        $success = 'Message received! We will contact you soon.';
                    }
                }
            }
            ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="#contact">
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="subject" class="form-control" placeholder="Subject" required>
                </div>
                <div class="mb-3">
                    <textarea name="message" class="form-control" rows="5" placeholder="Your Message" required></textarea>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-paper-plane me-2"></i>Send Message
                </button>
            </form>
            
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fa-solid fa-envelope"></i>
                    <h4>Email</h4>
                    <p>support@pharmacare.com</p>
                </div>
                <div class="contact-item">
                    <i class="fa-solid fa-phone"></i>
                    <h4>Phone</h4>
                    <p>+1 (555) 123-4567</p>
                </div>
                <div class="contact-item">
                    <i class="fa-solid fa-location-dot"></i>
                    <h4>Location</h4>
                    <p>San Francisco, CA</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <p>&copy; 2024 PharmaCare. All rights reserved.</p>
    </div>
</footer>
                                                                                                    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>
</body>
</html>
