<?php
// Database Setup Script for MALL OF CAP
// Run this file once to set up the database

require 'db.php';

if (!$db_connected) {
    die("Cannot connect to database. Please check your database configuration.\n");
}

try {
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS lazada CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE lazada");

    // Create tables
    $sql = file_get_contents('database.sql');
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }

    echo "<h2>✅ Database Setup Complete!</h2>";
    echo "<p>Your MALL OF CAP database has been successfully initialized.</p>";
    echo "<p><strong>Test Accounts:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Seller:</strong> seller1@gmail.com / password123</li>";
    echo "<li><strong>Consumer:</strong> consumer1@gmail.com / password123</li>";
    echo "</ul>";
    echo "<p><a href='index.php' class='btn btn-primary'>Go to Landing Page</a></p>";

} catch (Exception $e) {
    echo "<h2>❌ Database Setup Failed</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database configuration and try again.</p>";
}
?>
