<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$error = '';
$category = null;

// Handle delete
if ($action === 'delete' && isset($_GET['id'])) {
    // Note: We should also check if any products are using this category first
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id AND created_by = :uid");
    $stmt->execute(['id' => $_GET['id'], 'uid' => $user_id]);
    header("Location: categories.php");
    exit;
}

// Handle create/edit POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $id = $_POST['id'] ?? null;

    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        try {
            if ($id) { // Update
                $stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id AND created_by = :uid");
                $stmt->execute(['name' => $name, 'id' => $id, 'uid' => $user_id]);
            } else { // Create
                $stmt = $pdo->prepare("INSERT INTO categories (name, created_by) VALUES (:name, :uid)");
                $stmt->execute(['name' => $name, 'uid' => $user_id]);
            }
            header("Location: categories.php");
            exit;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                $error = "You already have a category with that name.";
            } else {
                $error = "An error occurred.";
            }
        }
    }
}

// Handle edit (fetch for form)
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id AND created_by = :uid");
    $stmt->execute(['id' => $_GET['id'], 'uid' => $user_id]);
    $category = $stmt->fetch();
    if (!$category) {
        die("Category not found.");
    }
}

// Fetch all categories for listing
$stmt = $pdo->prepare("SELECT * FROM categories WHERE created_by = :uid ORDER BY name");
$stmt->execute(['uid' => $user_id]);
$categories = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="style.css" />
<title>Categories - Dashboard</title>
</head>
<body>

<nav class="navbar">
  <div class="navbar-left">
    <span class="company-name">MALL OF CAP</span>
  </div>
  <div class="navbar-right">
    <button class="icon-button" title="Notifications" aria-label="Notifications">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M8 16a2 2 0 0 0 1.985-1.75H6.015A2 2 0 0 0 8 16zm.104-14.11c.058-.3-.12-.575-.43-.575-.318 0-.489.282-.43.575C7.522 1.488 7 2.863 7 4v2.5l-.5.5V7h3v-.5l-.5-.5V4c0-1.137-.522-2.512-1.396-2.11z"/><path d="M8 1a3 3 0 0 1 3 3v3.5c0 .5.5 1 1 1v.5h-8v-.5c.5 0 1-.5 1-1V4a3 3 0 0 1 3-3z"/></svg>
      <span class="notification-badge">3</span>
    </button>
    <button class="icon-button" title="Settings" aria-label="Settings">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/><path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.318.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.54 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.318c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.901 2.54-2.54l-.159-.292a.873.873 0 0 1 .52-1.255l.318-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.434-2.54-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.416.764-.42 1.6-1.185 1.184l-.292-.159a1.873 1.873 0 0 0-2.692 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.693-1.115l-.291.16c-.764.415-1.6-.42-1.184-1.185l.159-.292a1.873 1.873 0 0 0-1.116-2.692l-.318-.094c-.835-.246-.835-1.428 0 1.674l.319-.094a1.873 1.873 0 0 0 1.115-2.693l-.16-.291c-.416-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.116l.094-.318z"/></svg>
    </button>
    <button class="icon-button" title="Account" aria-label="Account" onclick="window.location.href='account.php'">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
    </button>
  </div>
</nav>

<aside class="sidebar">
  <ul class="sidebar-menu">
    <li><a href="dashboard.php"><span class="menu-icon">🏠</span> Home</a></li>
    <li><a href="dashboard.php"><span class="menu-icon">📦</span> Products</a></li>
    <li><a href="categories.php"><span class="menu-icon">🗂️</span> Categories</a></li>
    <li><a href="#"><span class="menu-icon">🏬</span> Stores</a></li>
  </ul>
  <ul class="sidebar-menu logout-menu">
    <li><a href="logout.php"><span class="menu-icon">🚪</span> Logout</a></li>
  </ul>
</aside>

<main class="main-content">
<h1>Manage Categories</h1>

<?php if ($error): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="categories.php">
  <h3><?php echo $category ? 'Edit Category' : 'Add New Category'; ?></h3>
  <input type="hidden" name="id" value="<?= $category['id'] ?? '' ?>" />
  <input type="text" name="name" placeholder="Category Name" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required />
  <button type="submit"><?php echo $category ? 'Update' : 'Create'; ?></button>
  <?php if ($category): ?>
    <a href="categories.php" class="button secondary-button">Cancel Edit</a>
  <?php endif; ?>
</form>

<h2>Your Categories</h2>
<?php if (empty($categories)): ?>
  <p>You have no categories yet.</p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categories as $cat): ?>
      <tr>
        <td><?= htmlspecialchars($cat['name']) ?></td>
        <td>
          <a href="categories.php?action=edit&id=<?= $cat['id'] ?>" class="action-link">Edit</a> |
          <a href="categories.php?action=delete&id=<?= $cat['id'] ?>" class="action-link delete-link" onclick="return confirm('Are you sure?');">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
</main>

</body>
</html>