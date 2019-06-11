<?php

namespace App\controllers;

use App\models\Task;
use Slim\Http\Request;
use Slim\Http\Response;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class TasksController {

    protected $c;

    /**
     * ImagesController constructor.
     * constructor receives container instance
     *
     * @param ContainerInterface $c
     */
    public function __construct(ContainerInterface $c) {
        $this->c = $c;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function tasks(Request $request, Response $response, array $args) {
        /** @var EntityManager $em */
        $em = $this->c->get('db');
        $items = $em->getRepository(Task::class)->findAll();
        $responseItems = array_map(function ($item) {
            /** @var Task $item */
            return ['id' => $item->getId(), 'name' => $item->getName()];
        }, $items);
        return $response->withJson(['status' => true, 'items' => $responseItems]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function add(Request $request, Response $response, array $args) {
        /** @var EntityManager $em */
        $em = $this->c->get('db');
        $postData = $request->getParsedBody();
        $task = new Task();
        $task->setName($postData['name']);
        $task->setImage($postData['image']);
        $task->setCluster($postData['cluster']);
        $task->setService($postData['name']);
        $task->setFamily($postData['name']);
        $task->setEnv($postData['env']);
        try {
            $em->persist($task);
            $em->flush();
        } catch (ORMException $e) {
            return $response->withJson(['status' => false, 'msg' => $e->getMessage()])->withStatus(500);
        }
        return $response->withJson(['status' => true, 'msg' => 'Success']);
    }
}