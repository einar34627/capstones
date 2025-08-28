<?php
session_start();

// Include database connection and auth class
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $userType = trim($_POST['user_type'] ?? '');
    $street = trim($_POST['street'] ?? '');
    
    // Validation
    if (empty($email) || empty($password) || empty($userType)) {
        $_SESSION['error'] = 'Please enter email, password, and select user type';
        header('Location: login');
        exit();
    }
    
    // Validate user type
    $validUserTypes = ['super_admin', 'sec_admin', 'admin'];
    if (!in_array($userType, $validUserTypes)) {
        $_SESSION['error'] = 'Invalid user type selected';
        header('Location: login');
        exit();
    }
    
    // Street validation for admin users
    if ($userType === 'admin' && empty($street)) {
        $_SESSION['error'] = 'Please select a street for Administrator login';
        header('Location: login');
        exit();
    }
    
    try {
        $auth = new Auth();
        
        // Attempt login
        if ($auth->attempt(['email' => $email, 'password' => $password])) {
            // Verify that the user's actual type matches the selected type
            $user = $auth->user();
            if ($user && $user['usertype'] === $userType) {
                // Store street information for admin users
                if ($userType === 'admin' && !empty($street)) {
                    $_SESSION['user_street'] = $street;
                }
                
                // Login successful - redirect based on user type
                $auth->redirectBasedOnRole();
            } else {
                // User type mismatch
                $auth->logout();
                $_SESSION['error'] = 'Selected user type does not match your account type';
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
