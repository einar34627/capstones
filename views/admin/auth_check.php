<?php

// Include authentication class
require_once __DIR__ . '/../../includes/Auth.php';

$auth = new Auth();

// Check if user is logged in
if (!$auth->check()) {
    $_SESSION['error'] = 'Please login to access this area.';
    header('Location: ../../login');
    exit();
}

// Check if user has admin or higher privileges
if (!$auth->hasMinimumRole('admin')) {
    $_SESSION['error'] = 'Access denied. Admin privileges required.';
    header('Location: ../../welcome');
    exit();
}

// Optional: Log access for security
$user = $auth->user();
if ($user) {
    error_log("Admin access: User {$user['email']} accessed admin area at " . date('Y-m-d H:i:s'));
}
?>
