<?php
declare(strict_types=1);

// Konfigurasi lama (root lokal XAMPP) tetap kompatibel agar proyek existing tidak terputus.
// Untuk memenuhi prinsip hak minimum, simpan akun DB khusus di config.local.php
// (disalin dari config.local.example.php); berkas tersebut diabaikan oleh Git.
// Variabel lingkungan DB_* tetap berprioritas paling tinggi.
$localDbSettings = [];
if (is_file(__DIR__ . '/config.local.php')) {
    $loadedSettings = require __DIR__ . '/config.local.php';
    if (is_array($loadedSettings)) {
        $localDbSettings = $loadedSettings;
    }
}
$dbSetting = static function (string $key, string $fallback) use ($localDbSettings): string {
    $environment = getenv($key);
    if ($environment !== false) {
        return (string) $environment;
    }
    return is_string($localDbSettings[$key] ?? null) ? $localDbSettings[$key] : $fallback;
};
define('DB_HOST', $dbSetting('DB_HOST', '127.0.0.1'));
define('DB_PORT', $dbSetting('DB_PORT', '3306'));
define('DB_NAME', $dbSetting('DB_NAME', 'etika_sosmed'));
define('DB_USER', $dbSetting('DB_USER', 'root'));
define('DB_PASS', $dbSetting('DB_PASS', ''));
unset($dbSetting, $loadedSettings, $localDbSettings);
define('APP_ROOT', __DIR__);

date_default_timezone_set('Asia/Jakarta');
ini_set('display_errors', '0');

// Menghasilkan /literasi-porto di XAMPP dan string kosong pada php -S.
$appDirectory = str_replace('\\', '/', realpath(APP_ROOT) ?: APP_ROOT);
$documentRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '');
$appDirectory = rtrim($appDirectory, '/');
$documentRoot = rtrim($documentRoot, '/');
$basePath = '';
if ($documentRoot !== '' && strcasecmp($appDirectory, $documentRoot) !== 0
    && strncasecmp($appDirectory, $documentRoot . '/', strlen($documentRoot) + 1) === 0) {
    $basePath = substr($appDirectory, strlen($documentRoot));
}
define('APP_BASE_PATH', $basePath);
unset($appDirectory, $documentRoot, $basePath);

if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Terminal pembuatan admin tidak memerlukan session maupun cookie.
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('etika_sosmed_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => APP_BASE_PATH . '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
