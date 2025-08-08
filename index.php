<?php
session_start();

// Include all necessary files
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Auth.php';
require_once __DIR__ . '/includes/Validator.php';
require_once __DIR__ . '/includes/helpers.php';

// Simple routing
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = dirname($script_name);

// Remove base path from request URI
$path = str_replace($base_path, '', $request_uri);
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');

// Remove any query string
$path = strtok($path, '?');

// Default route
if (empty($path)) {
    $path = 'welcome';
}

// Route mapping
$routes = [
    'welcome' => ['controller' => null, 'action' => 'welcome'],
    'login' => ['controller' => null, 'action' => 'login'],
    'admin' => ['controller' => null, 'action' => 'admin_dashboard'],
    'admin/settings' => ['controller' => null, 'action' => 'admin_settings'],
    'admin/reports' => ['controller' => null, 'action' => 'admin_reports'],
    'logout' => ['controller' => null, 'action' => 'logout'],
];

// Check if route exists
if (isset($routes[$path])) {
    $route = $routes[$path];
    
    if ($route['controller']) {
        $controller = new $route['controller']();
        $action = $route['action'];
        $controller->$action();
    } else {
        // Handle static pages
        switch ($route['action']) {
            case 'welcome':
                render('welcome');
                break;
            case 'login':
                render('login');
                break;
            case 'admin_dashboard':
                // Check if user is admin
                if (isset($_SESSION['user_id']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'admin') {
                    render('admin/dashboard', ['user' => $_SESSION]);
                } else {
                    redirect('login');
                }
                break;
            case 'admin_settings':
                // Check if user is admin
                if (isset($_SESSION['user_id']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'admin') {
                    render('admin/settings/index', ['user' => $_SESSION]);
                } else {
                    redirect('login');
                }
                break;
            case 'admin_reports':
                // Check if user is admin
                if (isset($_SESSION['user_id']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'admin') {
                    render('admin/reports/index', ['user' => $_SESSION]);
                } else {
                    redirect('login');
                }
                break;
            case 'logout':
                // Handle logout
                session_destroy();
                redirect('login');
                break;
            default:
                http_response_code(404);
                if (file_exists(__DIR__ . '/views/errors/404.php')) {
                    render('errors/404');
                } else {
                    echo "404 - Page not found";
                }
                break;
        }
    }
} else {
    // 404 Not Found
    http_response_code(404);
    if (file_exists(__DIR__ . '/views/errors/404.php')) {
        render('errors/404');
    } else {
        echo "404 - Page not found";
    }
} 