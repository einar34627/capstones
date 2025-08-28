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

// Check if user has super admin privileges
if (!$auth->isSuperAdmin()) {
    $_SESSION['error'] = 'Access denied. Super Admin privileges required.';
    header('Location: ../../welcome');
    exit();
}

// Optional: Log access for security
$user = $auth->user();
if ($user) {
    error_log("Super Admin access: User {$user['email']} accessed super admin area at " . date('Y-m-d H:i:s'));
}
?>
