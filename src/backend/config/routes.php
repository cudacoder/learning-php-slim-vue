<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/', function (Request $req, Response $res, array $args) {
    return $this->renderer->render($res, 'index.phtml', $args);
});

$app->group('/api', function () use ($app) {
    // GET
    $app->get('/stat', App\controllers\ApiController::class . ':stat');
    $app->get('/profiles', App\controllers\ApiController::class . ':profiles');
    $app->get('/projects', App\controllers\ApiController::class . ':projects');
    // POST
    $app->post('/init', App\controllers\ApiController::class . ':init');
    $app->post('/login', App\controllers\ApiController::class . ':login');
    // SUB-GROUPS
    $app->group('/tasks', function () use ($app) {
        $app->get('', App\controllers\TasksController::class . ':tasks');
        $app->post('/add', App\controllers\TasksController::class . ':add');
        $app->post('/container', App\controllers\TasksController::class . ':container');
    });
    $app->group('/images', function () use ($app) {
        $app->post('/create', App\controllers\ImagesController::class . ':create');
        $app->post('/build', App\controllers\ImagesController::class . ':build');
        $app->post('/push', App\controllers\ImagesController::class . ':push');
        $app->post('/version', App\controllers\ImagesController::class . ':version');
    });
    $app->group('/docker', function () use ($app) {
        $app->post('/deploy', App\controllers\DockerController::class . ':deploy');
    });
});

