<?php
/**
 * Alfiz Ilham Portfolio — Front Controller
 * All requests route through here.
 */

session_start();

// Load autoloader and bootstrap
require_once dirname(__DIR__) . '/bootstrap.php';

// Determine language
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
if (!in_array($lang, ['en', 'id'])) $lang = 'en';
$_SESSION['lang'] = $lang;

// Load i18n strings
i18n::load($lang);

// Track visitor
VisitorService::track();

// Register routes
$router = new Router();

// Page routes
$router->get('/', ['PageController', 'index']);

// API routes
$router->post('/api/contact', ['ApiController', 'contact']);
$router->get('/api/visitor', ['ApiController', 'visitorCount']);
$router->get('/api/tools', ['ApiController', 'tools']);
$router->get('/api/projects', ['ApiController', 'projects']);

// Language switcher
$router->get('/lang/{lang}', function () {
    $lang = $_GET['lang'] ?? 'en';
    if (in_array($lang, ['en', 'id'])) {
        $_SESSION['lang'] = $lang;
    }
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
});

// Get route from query string (set by .htaccess RewriteRule)
$uri = $_GET['/'] ?? '/';
$uri = trim($uri, '/');

// Dispatch route
$router->dispatch($uri);
