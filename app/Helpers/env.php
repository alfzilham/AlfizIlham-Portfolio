<?php
/** Minimal .env loader for this dependency-free application. */
function env_load($path)
{
    if (!is_readable($path)) return false;

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $position = strpos($line, '=');
        if ($position === false) continue;

        $key = trim(substr($line, 0, $position));
        $value = trim(substr($line, $position + 1));
        if ($key === '') continue;

        if (strlen($value) >= 2 && in_array($value[0], ["'", '"'], true) && $value[0] === substr($value, -1)) {
            $value = substr($value, 1, -1);
        } elseif (($comment = strpos($value, ' #')) !== false) {
            $value = trim(substr($value, 0, $comment));
        }

        $_ENV[$key] = $value;
        putenv($key . '=' . $value);
    }
    return true;
}

function env($key, $default = null)
{
    $value = $_ENV[$key] ?? getenv($key);
    return ($value === false || $value === null || $value === '') ? $default : $value;
}
