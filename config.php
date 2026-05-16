<?php
// Load .env file if it exists
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        // Parse key=value
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Remove quotes if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            $_ENV[$key] = $value;
        }
    }
}

// Configuration file for database credentials
// Values can come from .env file or use defaults

function env(string $key, $default = null)
{
    if (array_key_exists($key, $_ENV) && $_ENV[$key] !== '') {
        return $_ENV[$key];
    }
    $value = getenv($key);
    return $value === false ? $default : $value;
}

define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_PORT', env('DB_PORT', 3306));
define('DB_NAME', env('DB_NAME', 'smartwatch_db'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// Site configuration
define('SITE_NAME', env('SITE_NAME', 'SmartWatch Hub'));
define('SITE_URL', env('SITE_URL', 'http://localhost/SMARTWATCHES/'));
define('CURRENCY', env('CURRENCY', '$'));
define('MAIL_FROM_EMAIL', env('MAIL_FROM_EMAIL', 'noreply@smartwatchhub.local'));
define('MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'SmartWatch Hub'));
// Security & Session
define('SESSION_NAME', $_ENV['SESSION_NAME'] ?? 'smartwatch_session');
define('SESSION_LIFETIME', $_ENV['SESSION_LIFETIME'] ?? 3600);

// Application settings
define('ENVIRONMENT', $_ENV['ENVIRONMENT'] ?? 'development');
define('DEBUG', (strtolower($_ENV['DEBUG'] ?? 'false') === 'true'));

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'secure' => ENVIRONMENT === 'production',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}
