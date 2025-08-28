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

// URL decode the path to handle spaces and special characters
$path = urldecode($path);

// Default route
if (empty($path)) {
    $path = 'welcome';
}

// Route mapping
$routes = [
    'welcome' => ['controller' => null, 'action' => 'welcome'],
    'login' => ['controller' => null, 'action' => 'login'],
    'admin' => ['controller' => null, 'action' => 'admin_dashboard'],
    'admin/adashboard' => ['controller' => null, 'action' => 'admin_adashboard'],
    'admin/settings' => ['controller' => null, 'action' => 'admin_settings'],
    'admin/reports' => ['controller' => null, 'action' => 'admin_reports'],
    'admin/face_management' => ['controller' => null, 'action' => 'admin_face_management'],
    'admin/feedback_tip_management' => ['controller' => null, 'action' => 'admin_feedback_tip_management'],
    'sec admin/sadashboard' => ['controller' => null, 'action' => 'sec_admin_sadashboard'],
    'super_admin/dashboard' => ['controller' => null, 'action' => 'super_admin_dashboard'],
    'super_admin/surveillance' => ['controller' => null, 'action' => 'super_admin_surveillance'],
    'super_admin/face_management' => ['controller' => null, 'action' => 'super_admin_face_management'],
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
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
                    render('admin/adashboard', ['user' => $_SESSION]);
                } else {
                    redirect('login');
                }
                break;
            case 'admin_adashboard':
                // Check if user is admin
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
                    render('admin/adashboard', ['user' => $_SESSION]);
                } else {
                    redirect('login');
                }
                break;
            case 'admin_settings':
                // Check if user is admin
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
                    render('admin/settings/index', ['user' => $_SESSION]);
                } else {
                    redirect('login');
                }
                break;
            case 'admin_reports':
                // Check if user is admin
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
                    render('admin/reports/index', ['user' => $_SESSION]);
                } else {
                    redirect('login');
                }
                break;
            case 'admin_face_management':
                // Check if user is admin
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
                    render('admin/face_management/index', ['user' => $_SESSION]);
                } else {
                    redirect('login');
                }
                break;
            case 'admin_feedback_tip_management':
                // Check if user is admin
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
                    render('admin/feedback_tip_management/index', ['user' => $_SESSION]);
                } else {
                    redirect('login');
                }
                break;
            case 'sec_admin_sadashboard':
                // Check if user is sec_admin
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'sec_admin') {
                    render('sec admin/sadashboard', ['user' => $_SESSION]);
                } else {
                    redirect('login');
                }
                break;
            case 'super_admin_dashboard':
                // Check if user is super_admin
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'super_admin') {
                    render('super_admin/dashboard', ['user' => $_SESSION]);
                } else {
                    redirect('login');
                }
                break;
            case 'super_admin_surveillance':
                // Check if user is super_admin
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'super_admin') {
                    render('super_admin/facial_recognition/surveillance_panel', ['user' => $_SESSION]);
                } else {
                    redirect('login');
                }
                break;
            case 'super_admin_face_management':
                // Check if user is super_admin
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'super_admin') {
                    render('super_admin/facial_recognition/face_management', ['user' => $_SESSION]);
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