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
 * Class TaskContainerDefinitionObject
 *
 * @property Dto $name
 * @property Dto $image
 * @property Dto $cpu
 * @property Dto $memory
 * @property Dto $environment
 * @property Dto $memoryReservation
 * @property LogConfigurationDto $logConfiguration
 * @property ContainerPortMappingsDto[] $portMappings
 */
class ContainerDefinitionDto extends Dto {

    protected $schema = [
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string'],
            'image' => ['type' => 'string'],
            'cpu' => ['type' => 'integer'],
            'memory' => ['type' => 'integer'],
            'memoryReservation' => ['type' => 'integer'],
            'logConfiguration' => [
                '$ref' => 'App' . DS . 'Dto' . DS . 'LogConfigurationDto',
            ],
            'environment' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string'],
                        'value' => ['type' => 'string'],
                    ]
                ]
            ],
            'portMappings' => [
                'type' => 'array',
                'items' => [
                    '$ref' => 'App' . DS . 'Dto' . DS . 'ContainerPortMappingsDto',
                ]
            ]
        ],
        'default' => [
            'name' => '',
            'image' => '',
            'portMappings' => [
                [
                    'hostPort' => 80,
                    'containerPort' => 80
                ],
                [
                    'hostPort' => 22,
                    'containerPort' => 22
                ],
                [
                    'hostPort' => 443,
                    'containerPort' => 443
                ]
            ]
        ]
    ];
}