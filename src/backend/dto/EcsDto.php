<?php

namespace App\dto;

use Dto\Dto;

/**
 * Class EcsObject
 * @package Appli\Dto
 * @property Dto $service
 * @property Dto $cluster
 * @property Dto $load_balancer
 * @property Dto $version
 * @property Dto $image
 * @property Dto $target_group_arn
 * @property TaskDefinitionDto $task_definition
 */
class EcsDto extends Dto {

    protected $schema = [
        'type' => 'object',
        'additionalProperties' => false,
        'properties' => [
            'service' => ['type' => 'string'],
            'cluster' => ['type' => 'string'],
            'version' => ['type' => 'string'],
            'image' => ['type' => 'string'],
            'target_group_arn' => ['type' => 'string'],
            'task_definition' => [
                '$ref' => 'App' . DS . 'Dto' . DS . 'TaskDefinitionDto',
            ]
        ],
        'default' => [
            'service' => 'Cuda-Service',
            'version' => '1.0.0'
        ]
    ];
}