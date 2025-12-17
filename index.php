<?php
/**
 * Delícias da Fran - Application Bootstrap
 * 
 * This is the main entry point for the application.
 * It initializes the framework and routes requests.
 */

// Define base paths
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('VIEWS_PATH', ROOT_PATH . '/resources/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('DATABASE_PATH', ROOT_PATH . '/database');

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/Core/',
        APP_PATH . '/Controllers/',
        APP_PATH . '/Models/',
        APP_PATH . '/Middleware/',
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load configuration
$config = require CONFIG_PATH . '/app.php';
$dbConfig = require CONFIG_PATH . '/database.php';

// Initialize Database
require_once APP_PATH . '/Core/Database.php';
Database::initialize($dbConfig);

// Load helpers
require_once APP_PATH . '/Core/helpers.php';

// Initialize Router
require_once APP_PATH . '/Core/Router.php';
$router = new Router();

// Load routes
require_once ROOT_PATH . '/routes/web.php';

// Handle CSRF for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        http_response_code(419);
        die('CSRF token mismatch');
    }
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Run the router
$router->dispatch();
