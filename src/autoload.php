<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'Iriven\\PhpFormGenerator\\' => __DIR__ . '/',
        'Iriven\\PhpFormGenerator\\Tests\\' => dirname(__DIR__) . '/tests/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }

        $relative = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
});

require_once __DIR__ . '/legacy_aliases.php';
