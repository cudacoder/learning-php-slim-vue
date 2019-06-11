<?php

use Slim\Container;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

$container = $app->getContainer();
$container['fs'] = function (Container $c) {
    return new Filesystem();
};
$container['finder'] = function (Container $c) {
    return new Finder();
};
$container['renderer'] = function (Container $c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};
$container['db'] = function (Container $c) {
    $isDevMode = true;
    $config = Setup::createAnnotationMetadataConfiguration([__DIR__ . DS . '..' . DS . 'models'], $isDevMode);
    $conn = [
        'driver' => 'pdo_sqlite',
        'path' => $c->get('settings')['dbPath']
    ];
    return EntityManager::create($conn, $config);
};
$container['logger'] = function (Container $c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
