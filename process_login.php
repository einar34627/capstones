<?php
session_start();

// Include database connection
require_once __DIR__ . '/includes/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please enter both email and password';
        header('Location: login');
        exit();
    }
    
    try {
        $db = Database::getInstance();
        
        // Get user from database
        $user = $db->fetch("SELECT id, name, email, password, usertype FROM users WHERE email = ?", [$email]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Check if user is admin
            if ($user['usertype'] === 'admin') {
                // Login successful for admin
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['usertype'] = $user['usertype'];
                
                header('Location: admin');
                exit();
            } else {
                // Regular users are not allowed
                $_SESSION['error'] = 'Access denied. Only administrators can login.';
                header('Location: login');
                exit();
            }
        } else {
            // Login failed
            $_SESSION['error'] = 'Invalid email or password';
            header('Location: login');
            exit();
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = 'Login failed: ' . $e->getMessage();
        header('Location: login');
        exit();
    }
} else {
    // If not POST request, redirect to login page
    header('Location: login');
    exit();
}
?>
