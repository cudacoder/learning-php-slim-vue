<?php

namespace Tests\Unit;

use Tests\BaseTestCase;

/**
 * Class ApiControllerTest
 * @package Tests\Unit
 */
class ApiControllerTest extends BaseTestCase {

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testPostInit() {
        $response = $this->runRequest('POST', '/init', [
            'aws_profile' => 'default',
            'aws_access' => '',
            'aws_secret' => '',
            'aws_region' => 'eu-west-1'
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        if ($response->getBody()->eof()) {
            $response->getBody()->rewind();
        }
        $this->assertJson($response->getBody()->getContents());
    }

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testPostLogin() {
        $response = $this->runRequest('POST', '/login', ['profile' => 'default']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testGetStat() {
        $response = $this->runRequest('GET', '/stat');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testGetProfiles() {
        $response = $this->runRequest('GET', '/profiles');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testGetProjects() {
        $response = $this->runRequest('GET', '/projects');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
