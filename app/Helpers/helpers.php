<?php
/**
 * Helper Functions
 */

/**
 * Sanitize input
 */
function sanitize($value)
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to URL
 */
function redirect($url)
{
    header("Location: {$url}");
    exit;
}

/**
 * Send JSON response
 */
function json_response($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get current language
 */
function current_lang()
{
    return $_SESSION['lang'] ?? 'en';
}

/**
 * Get config value
 */
function config($key = null, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require CONFIG_PATH . '/config.php';
    }

    if ($key === null) return $config;

    $keys = explode('.', $key);
    $value = $config;
    foreach ($keys as $k) {
        if (!isset($value[$k])) return $default;
        $value = $value[$k];
    }
    return $value;
}

/**
 * Check if current page matches
 */
function is_active($section)
{
    return isset($_GET['section']) && $_GET['section'] === $section;
}
