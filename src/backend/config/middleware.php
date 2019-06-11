<?php
$app->add(function ($request, $response, $next) {
    /** @var Slim\Http\Response $response */
    $response = $next($request, $response);
    return $response->withHeader('Access-Control-Allow-Origin', [
        'http://localhost:8080'
    ]);
});