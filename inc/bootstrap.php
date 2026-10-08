<?php
declare(strict_types=1);
define('ROOT', dirname(__DIR__));
if (!is_file(ROOT . '/config.php')) {
    $b = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if (str_ends_with($b, '/admin')) $b = substr($b, 0, -6);
    header('Location: ' . rtrim($b, '/') . '/install.php');
    exit;
}
$CFG = require ROOT . '/config.php';
date_default_timezone_set($CFG['timezone'] ?? 'Asia/Kolkata');

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_name('xp_sess');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
session_start();

// base path (works in a sub-folder or at the domain root)
$docroot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
$rootReal = realpath(ROOT) ?: ROOT;
$base = '';
if ($docroot && str_starts_with(str_replace('\\', '/', $rootReal), str_replace('\\', '/', $docroot))) {
    $base = substr(str_replace('\\', '/', $rootReal), strlen(str_replace('\\', '/', $docroot)));
}
define('BASE', rtrim($base, '/'));

require ROOT . '/inc/db.php';
require ROOT . '/inc/helpers.php';
require ROOT . '/inc/schema.php';
try {
    if (setting('schema_v') !== '2') { migrate_schema(db(), $CFG['driver'] ?? 'mysql'); set_setting('schema_v', '2'); }
    if (setting('content_v') !== '3') { require_once __DIR__ . '/content_update.php'; try { content_update_v3(); set_setting('content_v', '3'); } catch (Throwable $e) { error_log('content_update_v3: ' . $e->getMessage()); } }
} catch (Throwable $e) { error_log('migrate: ' . $e->getMessage()); }
