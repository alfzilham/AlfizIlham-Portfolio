<?php
/**
 * Bootstrap — Autoloader + App Initialization
 */

define('ROOT_PATH', __DIR__);
define('PUBLIC_PATH', __DIR__ . '/public');
define('APP_PATH', __DIR__ . '/app');
define('VIEWS_PATH', __DIR__ . '/views');
define('LANG_PATH', __DIR__ . '/lang');
define('CONFIG_PATH', __DIR__ . '/config');
define('DATA_PATH', __DIR__ . '/data');

// Simple PSR-4-like autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to path
    $prefix = '';
    $baseDir = APP_PATH;

    if (strpos($class, 'App\\') === 0) {
        $prefix = 'App\\';
        $baseDir = APP_PATH;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . '/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load config
require_once CONFIG_PATH . '/config.php';

// Load local secrets before services use environment-based configuration.
require_once APP_PATH . '/Helpers/env.php';
env_load(CONFIG_PATH . '/.env');

// Load helpers
require_once APP_PATH . '/Helpers/helpers.php';
require_once APP_PATH . '/Helpers/i18n.php';
require_once APP_PATH . '/Helpers/session.php';

// Use root namespace for classes (simpler for vanilla PHP)
// Load core classes
require_once APP_PATH . '/Core/Router.php';
require_once APP_PATH . '/Core/Database.php';
require_once APP_PATH . '/Core/View.php';
require_once APP_PATH . '/Core/Request.php';

// Load models
require_once APP_PATH . '/Models/Project.php';
require_once APP_PATH . '/Models/Tool.php';
require_once APP_PATH . '/Models/Faq.php';
require_once APP_PATH . '/Models/Testimonial.php';
require_once APP_PATH . '/Models/Service.php';
require_once APP_PATH . '/Models/Gallery.php';
require_once APP_PATH . '/Models/Visitor.php';
require_once APP_PATH . '/Models/ShowcaseProject.php';
require_once APP_PATH . '/Models/Certificate.php';
require_once APP_PATH . '/Models/KnowledgeChunk.php';
require_once APP_PATH . '/Models/ChatFeedback.php';

// Load services
require_once APP_PATH . '/Services/ContactService.php';
require_once APP_PATH . '/Services/VisitorService.php';
require_once APP_PATH . '/Services/EmbeddingService.php';
require_once APP_PATH . '/Services/HttpClient.php';
require_once APP_PATH . '/Services/RagService.php';
require_once APP_PATH . '/Services/KnowledgeIndexService.php';

// Load controllers
require_once APP_PATH . '/Controllers/PageController.php';
require_once APP_PATH . '/Controllers/ApiController.php';
require_once APP_PATH . '/Controllers/AdminController.php';
require_once APP_PATH . '/Controllers/ChatController.php';
