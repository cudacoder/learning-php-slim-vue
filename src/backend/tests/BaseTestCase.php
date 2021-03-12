<?php

namespace Tests;

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use PHPUnit\Framework\TestCase;

define('DS', DIRECTORY_SEPARATOR);

/**
 * Base class for all Slim tests
 */
class BaseTestCase extends TestCase {

    /**
     * Use middleware when running application?
     * @var bool
     */
    protected $withMiddleware = false;

    /**
     * Slim app
     * @var App
     */
    protected $app;

    public function setUp(): void {
        // Use the application settings
        $settings = require __DIR__ . '/../config/settings.php';
        // Instantiate the application
        $app = new App($settings);
        $this->app = $app;
        // Set up dependencies
        require __DIR__ . '/../config/dependencies.php';
        // Register middleware
        if ($this->withMiddleware) {
            require __DIR__ . '/../config/middleware.php';
        }
        // Register routes
        require __DIR__ . '/../config/routes.php';
    }

    /**
     * Process the application given a request method and URI
     *
     * @param string $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string $requestUri the request URI
     * @param array|object|null $requestData the request data
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Slim\Exception\NotFoundException
     * @throws \Slim\Exception\MethodNotAllowedException
     */
    public function runRequest($requestMethod, $requestUri, $requestData = null) {
        // Create a mock environment for testing with
        $environment = Environment::mock(
            [
                'REQUEST_METHOD' => $requestMethod,
                'REQUEST_URI' => '/api' . $requestUri
            ]
        );
        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);
        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
        }
        // Set up a response object
        $response = new Response();
        // Process the application
        $response = $this->app->process($request, $response);
        // Return the response
        return $response;
    }
}
