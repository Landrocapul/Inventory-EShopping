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

$register_error = '';

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'consumer';

    if (empty($username) || empty($email) || empty($password)) {
        $register_error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $register_error = "Invalid email.";
    } elseif (!in_array($role, ['consumer', 'seller'])) {
        $register_error = "Invalid account type.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);

        if ($stmt->fetch()) {
            $register_error = "Username or email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
            $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hash, 'role' => $role]);
            // Auto-login after registration
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role === 'seller') {
                header("Location: dashboard.php");
            } else {
                header("Location: shop.php");
            }
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - MALL OF CAP</title>
    <meta name="description" content="Create your MALL OF CAP account">

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
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .register-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .register-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
        }

        .register-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .register-body {
            padding: 2rem;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-select {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
        }

        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-success-custom {
            background: var(--secondary-gradient);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 87, 108, 0.3);
            color: white;
        }

        .register-links {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .register-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .register-links a:hover {
            text-decoration: underline;
        }

        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-link:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .role-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .role-card {
            border: 2px solid #B22222;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .role-card:hover,
        .role-card.selected {
            border-color: #8B0000;
            background: rgba(139, 0, 0, 0.05);
        }

        .role-card i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #8B0000;
        }

        .register-header h2,
        .register-header p {
            color: white !important;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>

    <div class="register-container">
        <div class="register-header">
            <h2>Create Account</h2>
            <p>Join our community today</p>
        </div>

        <div class="register-body">
            <?php if ($register_error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($register_error); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="register.php" id="registerForm">
                <input type="hidden" name="action" value="register">

                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Choose a username" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Account Type</label>
                    <div class="role-cards">
                        <div class="role-card selected" data-role="consumer">
                            <i class="fas fa-shopping-bag"></i>
                            <div class="fw-semibold">Consumer</div>
                            <small class="text-muted">Shop for products</small>
                            <input type="radio" name="role" value="consumer" checked style="display: none;">
                        </div>
                        <div class="role-card" data-role="seller">
                            <i class="fas fa-store"></i>
                            <div class="fw-semibold">Seller</div>
                            <small class="text-muted">Manage inventory</small>
                            <input type="radio" name="role" value="seller" style="display: none;">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success-custom">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>
            </form>

            <div class="register-links">
                <div>
                    Already have an account? <a href="login.php">Sign In</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Role selection functionality
        document.querySelectorAll('.role-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remove selected class from all cards
                document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
                // Add selected class to clicked card
                this.classList.add('selected');
                // Update radio button
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Ensure back button always goes to index.php
        window.addEventListener('load', function() {
            const backLink = document.querySelector('.back-link');
            if (backLink) {
                backLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = 'index.php';
                });
            }
        });

        // Override browser back button to go to index.php
        window.addEventListener('popstate', function(event) {
            window.location.href = 'index.php';
        });
    </script>
</body>
</html>
