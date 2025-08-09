<?php
// Simple .env loader without external dependencies
// Loads key=value pairs from project root .env file into getenv/$_ENV/$_SERVER if not already set

$envFile = dirname(__DIR__) . '/.env';

if (is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        // Skip empty or commented lines
        if ($trimmed === '' || substr($trimmed, 0, 1) === '#') {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        $parts = explode('=', $line, 2);
        $name = trim($parts[0]);
        $value = isset($parts[1]) ? trim($parts[1]) : '';
        if ($name === '') {
            continue;
        }
        // Remove optional quotes
        $len = strlen($value);
        if ($len >= 2) {
            $first = substr($value, 0, 1);
            $last = substr($value, -1);
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }
        if (getenv($name) === false) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}