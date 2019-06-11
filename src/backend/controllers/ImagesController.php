<?php
/** @noinspection PhpUnusedParameterInspection */

namespace App\controllers;

use Aws\Sdk;
use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use App\models\AwsProfile;
use Aws\Api\DateTimeResult;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

/**
 * Class ImagesController
 * @package App\controllers
 */
class ImagesController {

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
    public function create(Request $request, Response $response, array $args) {
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
            $ecr->createRepository(['repositoryName' => $postData['repoName']])->toArray();
        } catch (Exception $e) {
            return $response->withJson(['msg' => $e->getMessage()])->withStatus(500);
        }
        return $response->withJson(['status' => true, 'msg' => 'Success']);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function version(Request $request, Response $response, array $args) {
        $postData = $request->getParsedBody();
        if (empty($postData['image'])) {
            return $response->withJson(['status' => true, 'version' => false]);
        }
        $repoNameArray = explode('/', $postData["image"]);
        if (count($repoNameArray) !== 3) {
            return $response->withJson(['status' => true, 'version' => false]);
        }
        /** @var EntityManager $em */
        $em = $this->c->get('db');
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
            $repoName = $repoNameArray[1] . '/' . $repoNameArray[2];
            $images = $sdk->createEcr()->describeImages(['repositoryName' => $repoName])->get('imageDetails');
            if (empty($images)) {
                return $response->withJson(['status' => true, 'version' => false]);
            }
        } catch (Exception $e) {
            return $response->withJson(['msg' => $e->getMessage()])->withStatus(500);
        }
        $currentVersion = $this->getLatestVersion($images);
        return $response->withJson([
            'status' => true,
            'version' => empty($currentVersion) ? false : $this->bumpVersion($currentVersion)
        ]);
    }

    /**
     * @param array $currentVersion
     *
     * @return string
     */
    private function bumpVersion($currentVersion) {
        $newImageVersion = explode(".", $currentVersion[0]);
        for ($i = count($newImageVersion) - 1; $i > -1; --$i) {
            if (++$newImageVersion[$i] < 10 || !$i) {
                break;
            }
            $newImageVersion[$i] = 0;
        }
        return implode(".", $newImageVersion);
    }

    /**
     * @param array $images
     *
     * @return array
     */
    private function getLatestVersion($images) {
        $latest = 0;
        $currentVersion = [];
        foreach ($images as $k => $image) {
            /** @var DateTimeResult $awsDateTime */
            $awsDateTime = $image['imagePushedAt'];
            $awsDateTimestamp = $awsDateTime->getTimestamp();
            if ($awsDateTimestamp > $latest) {
                $latest = $awsDateTimestamp;
                foreach ($image['imageTags'] as $imageTag) {
                    preg_match('/(\d)*\.(\d)*\.(\d)*$/', $imageTag, $ver);
                    if (empty($ver))
                        continue;
                    $currentVersion = $ver;
                }
            }
        }
        return $currentVersion;
    }
}