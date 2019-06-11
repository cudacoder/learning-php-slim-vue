<?php
// Doctrine's cli-config.php file is necessary for doctrine's development tools to work.
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration([__DIR__ . "/src/backend/models"], $isDevMode);

// database configuration parameters
$conn = [
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/src/backend/db.sqlite',
];

// obtaining the entity manager
/** @noinspection PhpUnhandledExceptionInspection */
$entityManager = EntityManager::create($conn, $config);
return ConsoleRunner::createHelperSet($entityManager);
