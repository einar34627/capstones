<?php
session_start();

// Include the Auth class
require_once __DIR__ . '/includes/Auth.php';

// Create Auth instance and logout
$auth = new Auth();
$auth->logout();

// Redirect to login page
header('Location: login');
exit();
?>
