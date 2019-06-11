<?php
/**
 * Created by PhpStorm.
 * User: gvw
 * Date: 1/22/2018
 * Time: 10:52 AM
 */

namespace App\dto;

use Dto\Dto;

/**
 * Class EcrObject
 * @package Appli\Dto
 * @property Dto $cpu
 * @property Dto $memory
 * @property Dto $family
 * @property Dto $networkMode
 * @property Dto $executionRoleArn
 * @property Dto $requiresCompatibilities
 * @property ContainerDefinitionDto[] $containerDefinitions
 */
class TaskDefinitionDto extends Dto {

    protected $schema = [
        'type' => 'object',
        'properties' => [
            'cpu' => ['type' => 'string'],
            'memory' => ['type' => 'string'],
            'family' => ['type' => 'string'],
            'networkMode' => ['type' => 'string'],
            'executionRoleArn' => ['type' => 'string'],
            'requiresCompatibilities' => [
                'type' => 'array',
                'items' => ['type' => 'string']
            ],
            'containerDefinitions' => [
                'type' => 'array',
                'items' => [
                    '$ref' => 'App' . DS . 'Dto' . DS . 'ContainerDefinitionDto'
                ]
            ],
        ],
        'default' => [
            'cpu' => '512',
            'memory' => '1024',
            'executionRoleArn' => 'arn:aws:iam::<id>:role/<role-name>',
            'requiresCompatibilities' => ['EC2']
        ]
    ];
}