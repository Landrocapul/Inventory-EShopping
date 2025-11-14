<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header('Location: shop.php');
    exit;
}

// Fetch product details
$product_stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name, u.username as seller_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN users u ON p.seller_id = u.id
    WHERE p.id = :id AND p.status = 'active'
");
$product_stmt->execute(['id' => $product_id]);
$product = $product_stmt->fetch();

if (!$product) {
    header('Location: shop.php');
    exit;
}

// Get cart count for navbar
$cart_count = 0;
if ($user_role === 'consumer') {
    $cart_stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = :uid");
    $cart_stmt->execute(['uid' => $user_id]);
    $cart_count = $cart_stmt->fetchColumn() ?? 0;
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart']) && $user_role === 'consumer') {
    $quantity = (int)$_POST['quantity'];

    if ($quantity > 0 && $quantity <= $product['stock_quantity']) {
        // Check if product already in cart
        $check_stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = :uid AND product_id = :pid");
        $check_stmt->execute(['uid' => $user_id, 'pid' => $product_id]);
        $existing = $check_stmt->fetch();

        if ($existing) {
            // Update quantity
            $new_quantity = $existing['quantity'] + $quantity;
            $update_stmt = $pdo->prepare("UPDATE cart SET quantity = :qty WHERE id = :id");
            $update_stmt->execute(['qty' => $new_quantity, 'id' => $existing['id']]);
        } else {
            // Add new item
            $insert_stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:uid, :pid, :qty)");
            $insert_stmt->execute(['uid' => $user_id, 'pid' => $product_id, 'qty' => $quantity]);
        }

        $success_message = "Product added to cart successfully!";
        // Update cart count
        $cart_stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = :uid");
        $cart_stmt->execute(['uid' => $user_id]);
        $cart_count = $cart_stmt->fetchColumn() ?? 0;
    } else {
        $error_message = "Invalid quantity or insufficient stock.";
    }
}

// Get related products (same category, different product)
$related_stmt = $pdo->prepare("
    SELECT p.id, p.name, p.price, p.stock_quantity
    FROM products p
    WHERE p.category_id = :cat_id AND p.id != :prod_id AND p.status = 'active' AND p.stock_quantity > 0
    ORDER BY RAND()
    LIMIT 4
");
$related_stmt->execute(['cat_id' => $product['category_id'], 'prod_id' => $product_id]);
$related_products = $related_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title><?= htmlspecialchars($product['name']) ?> - MALL OF CAP</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a href="shop.php" class="navbar-brand">MALL OF CAP</a>
            <div class="d-flex align-items-center">
                <?php if ($user_role === 'consumer'): ?>
                    <a href="shop.php?action=cart" class="btn btn-outline-primary me-3 position-relative">
                        🛒 Cart
                        <?php if ($cart_count > 0): ?>
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <a href="account.php" class="btn btn-outline-secondary me-3">👤 Account</a>
                <button class="btn btn-outline-secondary" title="Toggle Theme">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193-9.193a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707z"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="shop.php">Shop</a></li>
                <li class="breadcrumb-item"><a href="shop.php?category=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>

        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center justify-content-center bg-light" style="min-height: 400px;">
                        <div class="text-center text-muted">
                            <i class="fas fa-box fa-4x mb-3"></i>
                            <h4>Product Image</h4>
                            <p class="mb-0">High-quality product photography coming soon</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title mb-3"><?= htmlspecialchars($product['name']) ?></h1>

                        <div class="mb-3">
                            <span class="badge bg-primary fs-5">$<?= number_format($product['price'], 2) ?></span>
                        </div>

                        <div class="mb-3">
                            <h5>Description</h5>
                            <p class="card-text">
                                <?= nl2br(htmlspecialchars($product['description'] ?? 'No description available.')) ?>
                            </p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong>Category:</strong><br>
                                <span class="badge bg-secondary"><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></span>
                            </div>
                            <div class="col-sm-6">
                                <strong>Seller:</strong><br>
                                <span class="text-muted">👤 <?= htmlspecialchars($product['seller_name'] ?? 'Unknown') ?></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Stock Status:</strong><br>
                            <?php if ($product['stock_quantity'] > 10): ?>
                                <span class="badge bg-success">In Stock (<?= $product['stock_quantity'] ?> available)</span>
                            <?php elseif ($product['stock_quantity'] > 0): ?>
                                <span class="badge bg-warning text-dark">Low Stock (<?= $product['stock_quantity'] ?> left)</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Out of Stock</span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($product['tags'])): ?>
                            <div class="mb-4">
                                <strong>Tags:</strong><br>
                                <?php foreach (explode(',', $product['tags']) as $tag): ?>
                                    <span class="badge bg-light text-dark me-1 mb-1">#<?= htmlspecialchars(trim($tag)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Add to Cart Form (Only for consumers) -->
                        <?php if ($user_role === 'consumer'): ?>
                            <form method="post" action="product.php?id=<?= $product_id ?>" class="mb-3">
                                <div class="row g-3 align-items-end">
                                    <div class="col-auto">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" id="quantity" name="quantity" class="form-control"
                                               value="1" min="1" max="<?= $product['stock_quantity'] ?>"
                                               style="width: 80px;">
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg"
                                                <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                                            <i class="fas fa-cart-plus me-2"></i>
                                            <?= $product['stock_quantity'] <= 0 ? 'Out of Stock' : 'Add to Cart' ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>

                        <div class="d-flex gap-2">
                            <a href="shop.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                            </a>
                            <?php if ($user_role === 'consumer'): ?>
                                <a href="shop.php?action=cart" class="btn btn-outline-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>View Cart
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="mb-4">Related Products</h3>
                </div>
                <?php foreach ($related_products as $related): ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-box fa-2x mb-2"></i>
                                    <small>Product Image</small>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-2">
                                    <a href="product.php?id=<?= $related['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($related['name']) ?>
                                    </a>
                                </h6>
                                <div class="mb-2">
                                    <span class="badge bg-primary">$<?= number_format($related['price'], 2) ?></span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        Stock: <?= htmlspecialchars($related['stock_quantity']) ?>
                                    </small>
                                </div>
                                <div class="mt-auto">
                                    <a href="product.php?id=<?= $related['id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Theme toggle functionality
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggle = document.querySelector('.btn-outline-secondary');
            const currentTheme = localStorage.getItem('theme') || 'light';

            if (currentTheme === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
            }

            themeToggle.addEventListener('click', () => {
                const currentTheme = document.body.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                document.body.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
