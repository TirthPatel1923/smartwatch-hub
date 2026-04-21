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

define('DB_HOST', $_ENV['DB_HOST'] ?? '127.0.0.1');
define('DB_PORT', $_ENV['DB_PORT'] ?? 3306);
define('DB_NAME', $_ENV['DB_NAME'] ?? 'smartwatch_db');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

// Site configuration
define('SITE_NAME', $_ENV['SITE_NAME'] ?? 'SmartWatch Hub');
define('SITE_URL', $_ENV['SITE_URL'] ?? 'http://localhost/SMARTWATCHES/');
define('CURRENCY', $_ENV['CURRENCY'] ?? '$');

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
