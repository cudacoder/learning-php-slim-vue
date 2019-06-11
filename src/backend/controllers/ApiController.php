<?php
/** @noinspection PhpUnusedParameterInspection */

namespace App\controllers;

use Aws\Sdk;
use App\dto\VaultDto;
use GuzzleHttp\Client;
use Slim\Http\Request;
use Slim\Http\Response;
use App\models\AwsProfile;
use Doctrine\ORM\ORMException;
use GuzzleHttp\RequestOptions;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Finder\Finder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ApiController {

    protected $c;

    /**
     * ApiController constructor.
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
    public function init(Request $request, Response $response, array $args) {
        /** @var EntityManager $em */
        $em = $this->c->get('db');
        $postData = $request->getParsedBody();
        $awsProfile = new AwsProfile();
        $awsProfile->setName($postData['aws_profile']);
        $awsProfile->setAccess($postData['aws_access']);
        $awsProfile->setSecret($postData['aws_secret']);
        $awsProfile->setRegion($postData['aws_region']);
        $status = false;
        try {
            $em->persist($awsProfile);
            $em->flush();
            $status = true;
            $msg = 'Success';
        } catch (ORMException $e) {
            $msg = $e->getMessage();
        }
        return $response->withJson(['status' => $status, 'msg' => $msg]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function login(Request $request, Response $response, array $args) {
        /** @var EntityManager $em */
        $em = $this->c->get('db');
        $postData = $request->getParsedBody();
        try {
            /** @var AwsProfile $profile */
            $profile = $em->find(AwsProfile::class, (int) $postData['profile']);
            $sdk = new Sdk([
                'version' => 'latest',
                'region' => $profile->getRegion(),
                'credentials' => [
                    'key' => $profile->getAccess(),
                    'secret' => $profile->getSecret(),
                ]
            ]);
            $ecr = $sdk->createEcr();
            $authData = $ecr->getAuthorizationToken()->get('authorizationData');
            $authTokenDecoded = base64_decode($authData[0]['authorizationToken']);
            $authDataArray = explode(':', $authTokenDecoded);
            $url = $authData[0]['proxyEndpoint'];
            $dockerLogin = Process::fromShellCommandline("docker login -u {$authDataArray[0]} -p {$authDataArray[1]} {$url}");
            $dockerLogin->mustRun();
        } catch (ProcessFailedException $e) {
            preg_match("/Error\sOutput:(\s|.)*/", $e->getMessage(), $matches);
            return $response->withJson($matches)->withStatus(500);
        } catch (\Exception $e) {
            return $response->withJson(['msg' => $e->getMessage()])->withStatus(500);
        }
        return $response->withJson(['status' => true]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function stat(Request $request, Response $response, array $args) {
        /** @var EntityManager $em */
        $em = $this->c->get('db');
        if (!empty($em->getRepository(AwsProfile::class)->findAll())) {
            return $response->withJson(['status' => true, 'init' => true]);
        }
        return $response->withJson(['status' => true, 'init' => false]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function profiles(Request $request, Response $response, array $args) {
        /** @var EntityManager $em */
        $em = $this->c->get('db');
        $items = $em->getRepository(AwsProfile::class)->findAll();
        $responseItems = array_map(function ($item) {
            /** @var AwsProfile $item */
            return ['id' => $item->getId(), 'name' => $item->getName()];
        }, $items);
        return $response->withJson(['status' => true, 'items' => $responseItems]);
    }

    /**
     * @param Request $req
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function projects(Request $req, Response $response, array $args) {
        $projects = [];
        /** @var Finder $finder */
        $finder = new Finder();
        $finder->directories()->in(__DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . 'build')->depth(0);
        foreach ($finder as $folder) {
            $projects[] = $folder->getBasename();
        }
        return $response->withJson(['status' => true, 'items' => $projects]);
    }
}
