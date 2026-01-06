<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'biggestlooser');
define('DB_USER', 'root');
define('DB_PASS', '');

define('APP_NAME', 'Abnehm-App');
// Auto-detect base path for subdirectory installs.
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = $scriptName !== '' ? str_replace('\\', '/', dirname($scriptName)) : '';
if ($basePath === '/' || $basePath === '.') {
    $basePath = '';
}
define('APP_URL', $basePath);

define('UPLOAD_DIR', __DIR__ . '/../public/uploads');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024);
