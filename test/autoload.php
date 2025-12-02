<?php
spl_autoload_register(function ($className) {
    $classPath = str_replace('\\', '/', $className) . '.php';
    $paths = [
        __DIR__ .'/../src/lib/' . $classPath,
        __DIR__ . '/'. $classPath
    ];


    foreach ($paths as $path) {
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
});