<?php

// Include the Database class
require_once __DIR__ . '/Database.php';

class Auth {
    private $db;
    
    // Define admin hierarchy
    private $adminHierarchy = [
        'super_admin' => 3,
        'sec_admin' => 2,
        'admin' => 1,
        'user' => 0
    ];
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function attempt($credentials) {
        $email = $credentials['email'];
        $password = $credentials['password'];
        
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            $this->login($user);
            return true;
        }
        
        return false;
    }
    
    public function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_type'] = $user['usertype'];
        $_SESSION['user_level'] = $this->adminHierarchy[$user['usertype']] ?? 0;
    }
    
    public function logout() {
        session_destroy();
        session_start();
    }
    
    public function check() {
        return isset($_SESSION['user_id']);
    }
    
    public function user() {
        if (!$this->check()) {
            return null;
        }
        
        return $this->db->fetch(
            "SELECT * FROM users WHERE id = ?",
            [$_SESSION['user_id']]
        );
    }
    
    public function id() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function guest() {
        return !$this->check();
    }
    
    // Role-based access control methods
    public function hasRole($role) {
        if (!$this->check()) {
            return false;
        }
        
        return $_SESSION['user_type'] === $role;
    }
    
    public function hasAnyRole($roles) {
        if (!$this->check()) {
            return false;
        }
        
        return in_array($_SESSION['user_type'], $roles);
    }
    
    public function hasMinimumRole($minimumRole) {
        if (!$this->check()) {
            return false;
        }
        
        $userLevel = $_SESSION['user_level'] ?? 0;
        $requiredLevel = $this->adminHierarchy[$minimumRole] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }
    
    public function isSuperAdmin() {
        return $this->hasRole('super_admin');
    }
    
    public function isSecAdmin() {
        return $this->hasRole('sec_admin');
    }
    
    public function isAdmin() {
        return $this->hasRole('admin');
    }
    
    public function isAnyAdmin() {
        return $this->hasAnyRole(['super_admin', 'sec_admin', 'admin']);
    }
    
    public function getCurrentRole() {
        return $_SESSION['user_type'] ?? null;
    }
    
    public function getCurrentLevel() {
        return $_SESSION['user_level'] ?? 0;
    }
    
    // Redirect based on user role
    public function redirectBasedOnRole() {
        if (!$this->check()) {
            header('Location: login');
            exit();
        }
        
        switch ($_SESSION['user_type']) {
            case 'super_admin':
                header('Location: super_admin/dashboard');
                break;
            case 'sec_admin':
                header('Location: sec admin/sadashboard');
                break;
            case 'admin':
                header('Location: admin/adashboard');
                break;
            default:
                header('Location: welcome');
                break;
        }
        exit();
    }
} 