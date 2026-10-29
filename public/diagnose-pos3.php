<?php
/* Temporary, token-protected shared-hosting diagnostics. Remove after use. */
$base = dirname(__DIR__);
if (!is_file($base.'/vendor/autoload.php')) {
    http_response_code(503);
    exit("Diagnostic unavailable: Composer dependencies not found.\n");
}
require_once $base.'/vendor/autoload.php';
\Dotenv\Dotenv::createImmutable($base)->safeLoad();
$expectedToken = (string) ($_ENV['DIAGNOSE_TOKEN'] ?? $_SERVER['DIAGNOSE_TOKEN'] ?? '');
$providedToken = isset($_GET['token']) && is_string($_GET['token']) ? $_GET['token'] : '';
if ($expectedToken === '' || !hash_equals($expectedToken, $providedToken)) {
    http_response_code(404);
    exit("Not found\n");
}
header('Content-Type: text/plain; charset=utf-8');

$checks = [];
$check = static function (string $label, bool $ok, string $detail = '') use (&$checks): void {
    $checks[] = [$label, $ok, $detail];
};
$check('PHP version >= 8.2', version_compare(PHP_VERSION, '8.2.0', '>='), PHP_VERSION);
foreach (['ctype','curl','fileinfo','mbstring','openssl','pdo','tokenizer','xml'] as $extension) {
    $check('PHP extension: '.$extension, extension_loaded($extension));
}
$check('Composer autoloader', is_file($base.'/vendor/autoload.php'));
$check('Laravel bootstrap', is_file($base.'/bootstrap/app.php'));
$check('.env file', is_file($base.'/.env'));
foreach (['storage/framework/cache/data','storage/framework/sessions','storage/framework/views','storage/logs','bootstrap/cache'] as $directory) {
    $path = $base.'/'.$directory;
    $check('Writable: '.$directory, is_dir($path) && is_writable($path));
}
try {
    $app = require $base.'/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    $key = (string) config('app.key');
    $check('APP_KEY configured', str_starts_with($key, 'base64:') || strlen($key) >= 32);
    Illuminate\Support\Facades\DB::connection()->getPdo();
    $check('Database connection', true);
    foreach (['users','kategoris','produks','transaksis'] as $table) {
        $check('Database table: '.$table, Illuminate\Support\Facades\Schema::hasTable($table));
    }
} catch (Throwable $exception) {
    $check('Laravel/database bootstrap', false, 'Check .env settings, PHP extensions, and database tables.');
}
$failed = 0;
foreach ($checks as [$label, $ok, $detail]) {
    if (!$ok) { $failed++; }
    echo ($ok ? '[OK]   ' : '[FAIL] ').$label.($detail !== '' ? ' — '.$detail : '')."\n";
}
echo "\nResult: ".($failed === 0 ? 'READY' : $failed.' check(s) need attention')."\n";