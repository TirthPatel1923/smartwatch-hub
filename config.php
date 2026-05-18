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

            // Remove quotes
            $value = trim($value, "\"'");

            // Set environment variable correctly
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// DB CONFIG
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: 3306);
define('DB_NAME', getenv('DB_NAME') ?: 'smartwatch_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

// SITE CONFIG
define('SITE_NAME', getenv('SITE_NAME') ?: 'SmartWatch Hub');
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost/SMARTWATCHES/');
define('CURRENCY', getenv('CURRENCY') ?: '$');

// SECURITY
define('SESSION_NAME', getenv('SESSION_NAME') ?: 'smartwatch_session');
define('SESSION_LIFETIME', getenv('SESSION_LIFETIME') ?: 3600);

// ENVIRONMENT
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: 'development');
define('DEBUG', strtolower(getenv('DEBUG') ?: 'false') === 'true');

// SESSION SETUP
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