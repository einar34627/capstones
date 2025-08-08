<?php

// Helper functions

function redirect($url) {
    header("Location: {$url}");
    exit();
}

function back() {
    if (isset($_SERVER['HTTP_REFERER'])) {
        redirect($_SERVER['HTTP_REFERER']);
    }
    redirect('/');
}

function old($key, $default = '') {
    return $_SESSION['old'][$key] ?? $default;
}

function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function asset($path) {
    return "/assets/{$path}";
}

function url($path = '') {
    $base = rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '/');
    return $base . '/' . ltrim($path, '/');
}

function view($name, $data = []) {
    extract($data);
    $viewPath = __DIR__ . "/../views/{$name}.php";
    
    if (file_exists($viewPath)) {
        ob_start();
        include $viewPath;
        return ob_get_clean();
    } else {
        throw new Exception("View {$name} not found");
    }
}

function render($name, $data = []) {
    echo view($name, $data);
}

function is_admin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function is_user() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'user';
}

function auth_check() {
    return isset($_SESSION['user_id']);
}

function auth_user() {
    if (!auth_check()) {
        return null;
    }
    
    $user = new User();
    return $user->find($_SESSION['user_id']);
} 