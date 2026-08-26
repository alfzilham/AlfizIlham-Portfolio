<?php
/**
 * Env — minimal .env loader (no Composer dependencies).
 * Values are exposed via env() and also pushed to putenv()/$_ENV.
 */

/**
 * Parse a .env file into the environment.
 *
 * @param string $path Absolute path to the .env file
 * @return bool True if the file existed and was parsed
 */
function env_load($path)
{
    if (!is_readable($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);

        // Skip blanks and comments
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }

        $key = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        if ($key === '') {
            continue;
        }

        // Strip inline comments outside quotes
        if ($value !== '' && ($value[0] !== '"' && $value[0] !== "'")) {
            $hash = strpos($value, ' #');
            if ($hash !== false) {
                $value = trim(substr($value, 0, $hash));
            }
        }

        // Strip matching surrounding quotes
        $len = strlen($value);
        if ($len >= 2) {
            $first = $value[0];
            $last = $value[$len - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }

    return true;
}

/**
 * Get an environment variable.
 *
 * @param string $key     Variable name
 * @param mixed  $default Fallback when unset or empty
 * @return mixed
 */
function env($key, $default = null)
{
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }
    return $value;
}
