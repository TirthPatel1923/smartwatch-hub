<?php
require_once __DIR__ . '/config.php';

$databaseUrl = env('DATABASE_URL', env('MYSQL_URL', ''));

header('Content-Type: text/plain; charset=UTF-8');
echo "Render PHP health check\n";
echo "-----------------------\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "PHP version: " . PHP_VERSION . "\n";
echo "Site URL: " . SITE_URL . "\n";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_PORT: " . DB_PORT . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DATABASE_URL: " . $databaseUrl . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST', '[not set]') . "\n";
echo "MAIL_FROM_EMAIL: " . MAIL_FROM_EMAIL . "\n";
echo "Vendor autoload exists: " . (file_exists(__DIR__ . '/vendor/autoload.php') ? 'yes' : 'no') . "\n";
