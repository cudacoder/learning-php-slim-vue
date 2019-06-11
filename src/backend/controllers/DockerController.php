<?php
/** @noinspection PhpUnusedParameterInspection */

namespace App\controllers;

use Aws\Sdk;
use Exception;
use App\models\Task;
use Slim\Http\Request;
use Slim\Http\Response;
use App\models\AwsProfile;
use App\dto\TaskDefinitionDto;
use Doctrine\ORM\EntityManager;
use App\dto\LogConfigurationDto;
use App\dto\ContainerDefinitionDto;
use Psr\Container\ContainerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DockerController {

    const DEBUG = false;

    protected $c;

    // constructor receives container instance
    public function __construct(ContainerInterface $c) {
        $this->c = $c;
    }

    public function deploy(Request $request, Response $response, $args) {
        /** @var EntityManager $em */
        $em = $this->c->get('db');
        $postData = $request->getParsedBody();
        try {
            /** @var AwsProfile $profile */
            $profile = $em->find(AwsProfile::class, (int) $postData['profile']);
            /** @var Task $task */
            $task = $em->find(AwsProfile::class, (int) $postData['definition']);
            $sdk = new Sdk([
                'version' => 'latest',
                'region' => $profile->getRegion(),
                'credentials' => [
                    'key' => $profile->getAccess(),
                    'secret' => $profile->getSecret(),
                ]
            ]);
        } catch (Exception $e) {
            return $response->withJson(['msg' => $e->getMessage()])->withStatus(500);
        }
        $ecs = $sdk->createEcs();
        $mainContainer = new ContainerDefinitionDto();
        $logConfiguration = new LogConfigurationDto();
        /** @noinspection PhpUnhandledExceptionInspection */
        $logConfiguration->options->set('awslogs-region', $profile->getRegion());
        $mainContainer->logConfiguration = $logConfiguration;
        $mainContainer->name = $postData['project'];
        $mainContainer->image = $task->getImage();
        $mainContainer->environment = json_decode($task->getEnv(), true);
        $taskDefinition = new TaskDefinitionDto([
            'family' => $task->getFamily(),
            'containerDefinitions' => [$mainContainer]
        ]);
        if ($postData['fargate']) {
            $taskDefinition->requiresCompatibilities = ['FARGATE'];
            $taskDefinition->networkMode = "awsvpc";
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        $newTaskRevision = $ecs->registerTaskDefinition($taskDefinition->toArray())->search('taskDefinition.revision');
        $taskDefinitionFullname = $task->getFamily() . ':' . $newTaskRevision;
        $services = $ecs->describeServices([
            'cluster' => $task->getCluster(),
            'services' => [$task->getService()],
        ]);
        if ($services->get('services')[0]['status'] !== 'INACTIVE' || empty($services->get('failures'))) {
            $ecs->updateService([
                'service' => $task->getService(),
                'cluster' => $task->getCluster(),
                'taskDefinition' => $taskDefinitionFullname,
            ]);
            return $response->withJson(['status' => true, 'msg' => 'Success']);
        }
        try {
            $loadBalancerName = 'ELB-Name';
            $targetGroupArn = $this->createFargateTargetGroup($sdk, 'Foo-TG', 'vpcid');
            $loadBalancerArn = $sdk->createElasticLoadBalancingV2()
                ->describeLoadBalancers(['Names' => [$loadBalancerName]])
                ->search('LoadBalancers[0].LoadBalancerArn');
            $sdk->createElasticLoadBalancingV2()->createListener([
                'Port' => 81,
                'Protocol' => 'HTTP',
                'LoadBalancerArn' => $loadBalancerArn,
                'DefaultActions' => [['TargetGroupArn' => $targetGroupArn, 'Type' => 'forward']],
            ]);
            $sdk->createEcs()->createService([
                'cluster' => $task->getCluster(),
                'desiredCount' => 1,
                'launchType' => $postData['fargate'] ? 'FARGATE' : 'EC2',
                'serviceName' => $task->getService(),
                'taskDefinition' => $taskDefinitionFullname,
                'loadBalancers' => [
                    [
                        'containerPort' => 80,
                        'containerName' => $postData['project'],
                        'targetGroupArn' => $targetGroupArn,
                    ],
                ],
                'networkConfiguration' => [
                    'awsvpcConfiguration' => [
                        'assignPublicIp' => 'DISABLED',
                        'securityGroups' => [],
                        'subnets' => [],
                    ],
                ],
            ]);
        } catch (Exception $e) {
            return $response->withJson(['status' => true, 'msg' => $e->getMessage()])->withStatus(500);
        }
        return $response->withJson(['status' => true, 'msg' => 'Success']);
    }

    public function build(Request $request, Response $response, array $args) {
        /** @var EntityManager $em */
        $postData = $request->getParsedBody();
        $project = $postData['project'];
        $tag = empty($postData['tag']) ? 'latest' : $postData['tag'];
        $imgName = $postData['image'];
        $fwDockerfile = $postData['tag'] == 'debug' ? 'Debug' : 'Dockerfile';
        $buildCommand = 'docker build --no-cache';
        $buildContextDir = __DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . 'build' . DS . $project;
        $dockerfilePath = __DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . 'build' . DS . $project . DS . $fwDockerfile;
        $buildCommand .= " -t {$imgName}:{$tag}";
        $buildCommand .= " -f {$dockerfilePath} {$buildContextDir}";
        $process = (Process::fromShellCommandline($buildCommand))->setTimeout(null);
        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            preg_match("/Error\sOutput:(\s|.)*/", $e->getMessage(), $matches);
            return $response->withJson(['status' => false, 'msg' => $matches])->withStatus(500);
        }
        return $response->withJson(['status' => true, 'msg' => "Built image: {$imgName}:{$tag}"]);
    }

    /**
     * @param Sdk $sdk
     * @param string $name
     * @param string $vpcId
     *
     * @return string|null Target Group ARN
     */
    private function createFargateTargetGroup(Sdk $sdk, string $name, string $vpcId) {
        return $targetGroupArn = $sdk->createElasticLoadBalancingV2()->createTargetGroup([
            'Matcher' => ['HttpCode' => '200,302'],
            'Name' => $name,
            'Port' => 80,
            'Protocol' => 'HTTP',
            'TargetType' => 'ip',
            'VpcId' => $vpcId,
            'UnhealthyThresholdCount' => 10,
            'HealthCheckIntervalSeconds' => 30,
        ])->search('TargetGroups[0].TargetGroupArn');
    }
}