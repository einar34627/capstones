<?php
// Simple test file to check if everything is working
session_start();

echo "<h1>CAPS System Test</h1>";

// Test 1: Check if files exist
echo "<h2>1. File Structure Test</h2>";
$files = [
    'config/database.php',
    'includes/Database.php',
    'includes/Auth.php',
    'includes/User.php',
    'includes/Validator.php',
    'includes/helpers.php',
    'controllers/UserController.php',
    'views/login.php',
    'views/register.php',
    'views/welcome.php',
    'views/errors/404.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file} - EXISTS<br>";
    } else {
        echo "❌ {$file} - MISSING<br>";
    }
}

// Test 2: Check database connection
echo "<h2>2. Database Connection Test</h2>";
try {
    require_once 'includes/Database.php';
    $db = Database::getInstance();
    echo "✅ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 3: Check PHP version
echo "<h2>3. PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";

// Test 4: Check if XAMPP is running
echo "<h2>4. XAMPP Status</h2>";
if (function_exists('mysql_connect') || class_exists('PDO')) {
    echo "✅ MySQL extension available<br>";
} else {
    echo "❌ MySQL extension not available<br>";
}

echo "<h2>5. Next Steps</h2>";
echo "If all tests pass, you can access your application at:<br>";
echo "<a href='http://localhost/capstone/'>http://localhost/capstone/</a><br>";
echo "<a href='http://localhost/capstone/login'>http://localhost/capstone/login</a><br>";
echo "<a href='http://localhost/capstone/signup'>http://localhost/capstone/signup</a><br>";
?> 