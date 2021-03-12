<?php

define('DS', DIRECTORY_SEPARATOR);

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

$settings = require __DIR__ . '/../src/backend/config/settings.php';

$app = new \Slim\App($settings);

require __DIR__ . '/../src/backend/config/middleware.php';
require __DIR__ . '/../src/backend/config/dependencies.php';
require __DIR__ . '/../src/backend/config/routes.php';

$app->run();
