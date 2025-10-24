<?php
require_once __DIR__ . '/env.php';

// Ajusta la ruta si tu .env quedó fuera del webroot.
// Por ejemplo, si tu proyecto vive en htdocs\miapp y .env está en htdocs:
env_load(__DIR__ . '/../.env');

define('DB_USER',    env('DB_USER', ''));
define('DB_PASS',    env('DB_PASS', ''));
define('DB_CONN',    env('DB_CONN', ''));
define('DB_CHARSET', env('DB_CHARSET', 'AL32UTF8'));

// Validación temprana (falla en arranque si falta algo crítico)
foreach (['DB_USER','DB_PASS','DB_CONN'] as $k) {
    if (!constant($k)) {
        trigger_error("Falta variable requerida en .env: {$k}", E_USER_ERROR);
    }
}


function _env($key, $default = null) {
    // Usa tu env() si ya lo tienes. Este ejemplo es defensivo.
    if (function_exists('env')) {
        $v = env($key, $default);
    } else {
        $v = getenv($key);
        if ($v === false) $v = $default;
    }
    // Quita comillas simples/dobles y espacios invisibles
    return is_string($v) ? trim($v, " \t\n\r\0\x0B\"'") : $v;
}