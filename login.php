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

$login_error = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $user = trim($_POST['user']);
    $pass = $_POST['pass'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :user OR email = :user");
    $stmt->execute(['user' => $user]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($account && password_verify($pass, $account['password'])) {
        $_SESSION['user_id'] = $account['id'];
        $_SESSION['username'] = $account['username'];
        $_SESSION['role'] = $account['role'];

        // Redirect based on role
        if ($account['role'] === 'seller' || $account['role'] === 'admin') {
            header("Location: dashboard.php");
        } else {
            header("Location: shop.php");
        }
        exit;
    } else {
        $login_error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - MALL OF CAP</title>
    <meta name="description" content="Sign in to your MALL OF CAP account">

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
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .login-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
        }

        .login-header h2,
        .login-header p {
            color: white !important;
        }

        .login-body {
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

        .btn-primary-custom {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .login-links {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .login-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .login-links a:hover {
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
    </style>
</head>
<body>
    <a href="index.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>

    <div class="login-container">
        <div class="login-header">
            <h2>Welcome Back</h2>
            <p>Sign in to your account</p>
        </div>

        <div class="login-body">
            <?php if ($login_error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($login_error); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="mb-3">
                    <label for="user" class="form-label fw-semibold">Username or Email</label>
                    <input type="text" id="user" name="user" class="form-control" placeholder="Enter your username or email" required>
                </div>

                <div class="mb-4">
                    <label for="pass" class="form-label fw-semibold">Password</label>
                    <input type="password" id="pass" name="pass" class="form-control" placeholder="Enter your password" required>
                </div>

                <button type="submit" name="login" class="btn btn-primary-custom">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>

            <div class="login-links">
                <div class="mb-2">
                    <a href="forgot_password.php">Forgot Password?</a>
                </div>
                <div>
                    Don't have an account? <a href="register.php">Create Account</a>
                </div>
            </div>
        </div>
    </div>

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
