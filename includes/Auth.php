<?php

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function attempt($credentials) {
        $email = $credentials['email'];
        $password = $credentials['password'];
        
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = :email",
            ['email' => $email]
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
            "SELECT * FROM users WHERE id = :id",
            ['id' => $_SESSION['user_id']]
        );
    }
    
    public function id() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function guest() {
        return !$this->check();
    }
} 