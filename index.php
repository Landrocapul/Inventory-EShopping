<?php
session_start();
require 'db.php';

// If user is already logged in, redirect to appropriate page
if (isset($_SESSION['user_id'])) {
    if (in_array($_SESSION['role'], ['seller', 'admin'])) {
        header("Location: dashboard.php");
    } else {
        header("Location: shop.php");
    }
    exit;
}

// Get some stats for the landing page
$stats = ['total_products' => 0, 'total_sellers' => 0, 'total_categories' => 0, 'recent_products' => []];
$db_available = true;

try {
    $stats['total_products'] = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
    $stats['total_sellers'] = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'seller'")->fetchColumn();
    $stats['total_categories'] = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $stats['recent_products'] = $pdo->query("SELECT name, price FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT 6")->fetchAll();
} catch (Exception $e) {
    $db_available = false;
    // Keep default values
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MALL OF CAP - Your Premium Shopping Destination</title>
    <meta name="description" content="Discover amazing products from trusted sellers. Shop electronics, fashion, home goods and more at MALL OF CAP.">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css" />

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #8B0000 0%, #B22222 50%, #DC143C 100%);
            --secondary-gradient: linear-gradient(135deg, #8B0000 0%, #CD5C5C 100%);
            --accent-gradient: linear-gradient(135deg, #DC143C 0%, #FF6347 100%);
            --success-gradient: linear-gradient(135deg, #B22222 0%, #FF4500 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            color: #2d3748; /* Default dark text for light backgrounds */
        }

        .hero-section {
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            color: white !important;
            -webkit-text-fill-color: white !important;
            background: none !important;
            -webkit-background-clip: initial !important;
            text-align: left;
        }

        .hero-subtitle {
            font-size: clamp(1.1rem, 2vw, 1.4rem);
            font-weight: 400;
            margin-bottom: 2.5rem;
            opacity: 0.9;
            line-height: 1.6;
            color: white !important;
            text-align: left;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary-custom {
            background: var(--secondary-gradient);
            border: none;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
            color: white !important;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 87, 108, 0.4);
        }

        .btn-outline-custom {
            border: 2px solid white;
            color: white;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 50px;
            background: transparent;
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            background: white;
            color: #8B0000;
            transform: translateY(-2px);
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 20%;
            right: 15%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 100px;
            height: 100px;
            bottom: 15%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .features-section {
            padding: 5rem 0;
            background: #f8f9fa;
            color: #2d3748;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.2);
            color: #2d3748;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--accent-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .feature-title {
            font-weight: 700;
            margin-bottom: 1rem;
            color: #2d3748;
        }

        .stats-section {
            background: var(--primary-gradient);
            padding: 4rem 0;
            color: white;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            display: block;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 500;
        }

        .products-preview {
            padding: 5rem 0;
            background: white;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .product-card-preview {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .product-card-preview:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .product-image-placeholder {
            height: 200px;
            background: var(--accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .product-info {
            padding: 1.5rem;
            color: #2d3748; /* Ensure dark text */
        }

        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #e53e3e; /* Keep price in red */
        }

        /* Override any inherited white colors */
        .product-card-preview *,
        .product-card-preview h4 {
            color: #2d3748 !important;
        }

        /* Keep price red */
        .product-card-preview .product-price {
            color: #e53e3e !important;
        }

        .cta-section {
            background: var(--primary-gradient);
            padding: 5rem 0;
            text-align: center;
            color: white;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: white !important;
        }

        .cta-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            opacity: 0.9;
            color: white !important;
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .navbar-brand-custom {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: #8B0000; /* Fallback for browsers that don't support gradient text */
        }

        /* Custom navbar button styling - matching btn-primary-custom */
        .navbar-custom .btn-outline-dark-red,
        .navbar-custom .btn-outline-secondary {
            background: var(--secondary-gradient);
            border: none;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
            color: white;
        }

        .navbar-custom .btn-outline-dark-red:hover,
        .navbar-custom .btn-outline-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 87, 108, 0.4);
            background: var(--secondary-gradient);
        }

        .modal-content-custom {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        @media (max-width: 768px) {
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-primary-custom, .btn-outline-custom {
                width: 100%;
                max-width: 300px;
            }
        }

        /* Specific text color overrides for proper visibility */
        .features-section h2.display-4,
        .products-preview h2.display-4 {
            color: #2d3748 !important;
        }

        .features-section .lead,
        .products-preview .lead {
            color: #6c757d !important;
        }

        .feature-title {
            color: #2d3748 !important;
        }

        .feature-card p {
            color: #6c757d !important;
        }

        .product-title {
            color: #2d3748 !important;
        }

        /* Ensure all text in feature cards is visible */
        .feature-card,
        .feature-card * {
            color: inherit;
        }

        /* Ensure all text in product cards is visible */
        .product-card-preview,
        .product-card-preview * {
            color: inherit;
        }

        /* Override any Bootstrap text classes that might cause issues */
        .features-section .text-dark,
        .products-preview .text-dark {
            color: #2d3748 !important;
        }

        .features-section .text-muted,
        .products-preview .text-muted {
            color: #6c757d !important;
        }

        /* CTA section button styling - matching btn-primary-custom */
        .cta-section .btn-light {
            background: var(--secondary-gradient);
            border: none;
            color: white;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .cta-section .btn-light:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 87, 108, 0.4);
            background: var(--secondary-gradient);
            color: white;
            text-decoration: none;
        }

        /* Footer text styling - make all text white */
        footer.bg-dark {
            color: white !important;
        }

        footer.bg-dark h5,
        footer.bg-dark h6 {
            color: white !important;
        }

        footer.bg-dark p {
            color: white !important;
        }

        footer.bg-dark .text-muted {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        footer.bg-dark a {
            color: white !important;
        }

        footer.bg-dark a:hover {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        footer.bg-dark .text-light {
            color: white !important;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand navbar-brand-custom" href="#">MALL OF CAP</a>

            <div class="d-flex align-items-center gap-3">
                <a href="login.php" class="btn btn-outline-dark-red btn-sm">
                    <i class="fas fa-sign-in-alt me-1"></i>Sign In
                </a>

                <a href="register.php" class="btn btn-primary-custom btn-sm">
                    Get Started
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>

        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">
                            Discover Amazing Products from Trusted Sellers
                        </h1>
                        <p class="hero-subtitle">
                            Join thousands of customers shopping for electronics, fashion, home goods, and more.
                            Connect with local sellers and find exactly what you need.
                        </p>

                        <div class="hero-buttons">
                            <a href="login.php" class="btn btn-primary-custom">
                                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                            </a>
                            <a href="register.php" class="btn btn-outline-custom">
                                <i class="fas fa-store me-2"></i>Become a Seller
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-4 fw-bold text-dark mb-3">Why Choose MALL OF CAP?</h2>
                    <p class="lead text-muted">Experience shopping like never before with our innovative platform</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Secure Shopping</h3>
                        <p class="text-muted">Your transactions are protected with bank-level security. Shop with confidence knowing your data is safe.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h3 class="feature-title">Fast Delivery</h3>
                        <p class="text-muted">Get your orders delivered quickly with our reliable shipping partners. Track your packages in real-time.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="feature-title">Local Sellers</h3>
                        <p class="text-muted">Connect with trusted local sellers. Support your community while getting authentic, quality products.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <?php if (!$db_available): ?>
                <div class="alert alert-info text-center mb-4" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Demo Mode:</strong> Database not available. Registration and login are disabled.
                    <br><small><a href="setup.php" style="color: #87ceeb; text-decoration: underline;">Click here to set up the database</a></small>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo number_format($stats['total_products']); ?>+</span>
                        <span class="stat-label">Products Available</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo number_format($stats['total_sellers']); ?>+</span>
                        <span class="stat-label">Trusted Sellers</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo number_format($stats['total_categories']); ?>+</span>
                        <span class="stat-label">Categories</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Customer Support</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Preview -->
    <?php if (!empty($stats['recent_products'])): ?>
    <section class="products-preview">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-4 fw-bold text-dark mb-3">Featured Products</h2>
                    <p class="lead text-muted">Discover our latest additions to the marketplace</p>
                </div>
            </div>

            <div class="product-grid">
                <?php foreach ($stats['recent_products'] as $product): ?>
                <div class="product-card-preview">
                    <div class="product-image-placeholder">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="product-info">
                        <h4 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h4>
                        <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-5">
                <a href="login.php" class="btn btn-primary-custom">
                    View All Products
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Ready to Start Your Shopping Journey?</h2>
            <p class="cta-subtitle">Join thousands of satisfied customers and discover amazing products today</p>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="register.php" class="btn btn-light btn-lg px-4">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </a>
                <a href="login.php" class="btn btn-outline-light btn-lg px-4">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="text-uppercase fw-bold mb-3">MALL OF CAP</h5>
                    <p class="text-muted">Your trusted marketplace connecting customers with quality products from local sellers.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">About Us</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">How It Works</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Support</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Contact</a></li>
                    </ul>
                </div>

                <div class="col-md-2 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3">Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Electronics</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Fashion</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Home & Garden</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Sports</a></li>
                    </ul>
                </div>

                <div class="col-md-4 mb-4">
                    <h6 class="text-uppercase fw-bold mb-3">Stay Connected</h6>
                    <p class="text-muted">Subscribe to our newsletter for the latest updates and exclusive offers.</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Enter your email">
                        <button class="btn btn-primary" type="button">Subscribe</button>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2024 MALL OF CAP. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-muted text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
