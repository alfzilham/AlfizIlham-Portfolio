<?php
/**
 * Alfiz Ilham Portfolio — Front Controller
 * All requests route through here.
 */

// Load autoloader and bootstrap
require_once dirname(__DIR__) . '/bootstrap.php';

// Hardened session (cookie flags + CSRF token init)
secure_session_start();

// Determine language
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? config('default_language', 'id');
if (!in_array($lang, ['en', 'id'])) $lang = config('default_language', 'id');
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
$router->post('/api/chat', ['ChatController', 'ask']);

// Showcase cards (public read)
$router->get('/api/cards', ['AdminController', 'listCards']);

// Editor mode — admin auth
$router->post('/api/admin/login', ['AdminController', 'login']);
$router->post('/api/admin/logout', ['AdminController', 'logout']);
$router->get('/api/admin/session', ['AdminController', 'session']);

// Showcase cards CRUD (admin only)
$router->post('/api/admin/cards', ['AdminController', 'createCard']);
$router->post('/api/admin/cards/{id}', ['AdminController', 'updateCard']);
$router->delete('/api/admin/cards/{id}', ['AdminController', 'deleteCard']);

// Certificates
$router->get('/api/admin/certificates', ['AdminController', 'listCertificates']);
$router->post('/api/admin/certificates', ['AdminController', 'createCertificate']);
$router->post('/api/admin/certificates/{id}', ['AdminController', 'updateCertificate']);
$router->delete('/api/admin/certificates/{id}', ['AdminController', 'deleteCertificate']);
$router->post('/api/admin/certificates/{id}/pin', ['AdminController', 'toggleCertificatePin']);
$router->post('/api/admin/certificates/bulk-import', ['AdminController', 'bulkImportCertificates']);
$router->post('/api/admin/projects/bulk-import', ['AdminController', 'bulkImportProjects']);
$router->get('/api/admin/certificates/export', ['AdminController', 'exportCertificates']);
$router->get('/api/admin/projects/export', ['AdminController', 'exportProjects']);

// Language switcher
$router->get('/lang/{lang}', function () {
    $lang = $_GET['lang'] ?? 'en';
    if (in_array($lang, ['en', 'id'])) {
        $_SESSION['lang'] = $lang;
    }
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
});

// Resolve route URI — supports both pretty URLs (/api/x) and legacy (index.php?/api/x)
function resolve_route_uri()
{
    // 1) Legacy style: query contains a key starting with "/" (e.g. ?/api/visitor or ?/=path)
    foreach ($_GET as $key => $value) {
        if ($key === '/') {
            return trim((string) $value, '/');
        }
        if (strpos($key, '/') === 0) {
            return trim($key, '/');
        }
    }

    // 2) Pretty URL: derive path from REQUEST_URI minus base directory
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    if ($base !== '' && strpos($path, $base) === 0) {
        $path = substr($path, strlen($base));
    }
    $path = ltrim($path, '/');

    return $path === 'index.php' ? '/' : rtrim($path, '/');
}

// Get route from request
$uri = resolve_route_uri();

// Dispatch route
$router->dispatch($uri);
