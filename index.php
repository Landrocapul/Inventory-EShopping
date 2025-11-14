<?php
session_start();
require 'db.php';

$register_error = '';
$login_error = '';

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

// Determine which form to show by default
$show_register = isset($_GET['register']);
if (!$show_register) {
    // Also show register if there was a register error
    if ($register_error) $show_register = true;
    // Or if login error, show login
    if ($login_error) $show_register = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="style.css" />

<title>Login & Register</title>
<script>
  function toggleForms() {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    if (loginForm.classList.contains('hidden')) {
      loginForm.classList.remove('hidden');
      registerForm.classList.add('hidden');
    } else {
      loginForm.classList.add('hidden');
      registerForm.classList.remove('hidden');
    }
  }
</script>
</head>
<body>
<nav class="navbar">
  <div class="navbar-left">
    <span class="company-name">MALL OF CAP</span>
  </div>
  <div class="navbar-right">
    <button class="icon-button theme-toggle" title="Toggle Theme" aria-label="Toggle Theme">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193-9.193a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707z"/>
      </svg>
    </button>
  </div>
</nav>

<section class="box">
<div id="login-form" class="<?= $show_register ? 'hidden' : '' ?>">
  <h2>Login</h2>
  <?php if ($login_error): ?><div class="error"><?= htmlspecialchars($login_error) ?></div><?php endif; ?>
  <form method="post" action="">
    <input type="text" name="user" placeholder="Username or Email" required>
    <input type="password" name="pass" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
  </form>
  <div class="forgot-link">
    <a href="forgot_password.php">Forgot Password?</a>
  </div>
  <div class="toggle-link">
    Don't have an account? <a onclick="toggleForms()">Register here</a>
  </div>
</div>

<div id="register-form" class="<?= $show_register ? '' : 'hidden' ?>">
  <h2>Register</h2>
  <?php if ($register_error): ?><div class="error"><?= htmlspecialchars($register_error) ?></div><?php endif; ?>
  <form method="post" action="index.php">
    <input type="hidden" name="action" value="register">
    <div class="form-group">
      <label for="reg_username">Username</label>
      <input type="text" id="reg_username" name="username" required>
    </div>
    <div class="form-group">
      <label for="reg_email">Email</label>
      <input type="email" id="reg_email" name="email" required>
    </div>
    <div class="form-group">
      <label for="reg_password">Password</label>
      <input type="password" id="reg_password" name="password" required>
    </div>
    <div class="form-group">
      <label for="role">Account Type</label>
      <select name="role" id="role" required>
        <option value="consumer">Consumer (Shop for products)</option>
        <option value="seller">Seller (Manage inventory)</option>
      </select>
    </div>
    <button type="submit">Register</button>
  </form>
  <p>Already have an account? <a href="#" onclick="toggleForms()">Login here</a></p>
</div>
</section>

<script>
// Theme toggle functionality
document.addEventListener('DOMContentLoaded', () => {
  const themeToggle = document.querySelector('.theme-toggle');
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

</body>
</html>
