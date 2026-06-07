<?php
/**
 * Front Controller
 * 
 * Single entry point for all HTTP requests.
 * Starts session, loads configuration, instantiates the Router,
 * registers routes, and dispatches the current request.
 */

// Load helpers first (Session needed before start)
require_once __DIR__ . '/helpers/Session.php';

// Start session with timeout management
Session::start();

// Load configuration
require_once __DIR__ . '/config/database.php';

// Autoload controllers
spl_autoload_register(function ($className) {
    $paths = [
        __DIR__ . '/controllers/' . $className . '.php',
        __DIR__ . '/models/' . $className . '.php',
        __DIR__ . '/helpers/' . $className . '.php',
        __DIR__ . '/middleware/' . $className . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Load Router class
require_once __DIR__ . '/helpers/Router.php';

// Instantiate Router
$router = new Router();

// Register routes
require_once __DIR__ . '/config/routes.php';

// Get request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Dispatch the request
$router->dispatch($method, $uri);
