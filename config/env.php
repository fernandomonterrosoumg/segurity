<?php
// config/env.php
if (!function_exists('env_load')) {
    function env_load(string $path): void {
        if (!is_file($path)) return;
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;

            if (!preg_match('/^\s*([A-Z0-9_\.]+)\s*=\s*(.*)\s*$/i', $line, $m)) continue;
            $key = $m[1];
            $val = $m[2];

            // Soporta comillas "..." o '...'
            if (($val[0] ?? '') === '"' && substr($val, -1) === '"') {
                $val = stripcslashes(substr($val, 1, -1));
            } elseif (($val[0] ?? '') === "'" && substr($val, -1) === "'") {
                $val = substr($val, 1, -1);
            }

            // No sobrescribas si ya viene del sistema/Apache
            if (getenv($key) === false && !isset($_ENV[$key])) {
                putenv("$key=$val");
                $_ENV[$key] = $val;
                $_SERVER[$key] = $val;
            }
        }
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        $v = getenv($key);
        if ($v === false) $v = $_ENV[$key] ?? $_SERVER[$key] ?? null;
        return $v ?? $default;
    }
}
