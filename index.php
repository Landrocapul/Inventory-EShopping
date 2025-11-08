<?php
session_start();
require 'db.php';

$register_error = '';
$login_error = '';

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $register_error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $register_error = "Invalid email.";
    } elseif ($password !== $confirm) {
        $register_error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);

        if ($stmt->fetch()) {
            $register_error = "Username or email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hash]);
            // Optionally redirect to login or auto-login
            header("Location: index.php?login=1");
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
        header("Location: dashboard.php");
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
<section class="box">
<div id="login-form" class="<?= $show_register ? 'hidden' : '' ?>">
  <h2>Login</h2>
  <?php if ($login_error): ?><div class="error"><?= htmlspecialchars($login_error) ?></div><?php endif; ?>
  <form method="post" action="">
    <input type="text" name="user" placeholder="Username or Email" required>
    <input type="password" name="pass" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
  </form>
  <div class="toggle-link">
    Don't have an account? <a onclick="toggleForms()">Register here</a>
  </div>
</div>

<div id="register-form" class="<?= $show_register ? '' : 'hidden' ?>">
  <h2>Register</h2>
  <?php if ($register_error): ?><div class="error"><?= htmlspecialchars($register_error) ?></div><?php endif; ?>
  <form method="post" action="">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <button type="submit" name="register">Register</button>
  </form>
  <div class="toggle-link">
    Already have an account? <a onclick="toggleForms()">Login here</a>
  </div>
</div>
</section>

</body>
</html>
