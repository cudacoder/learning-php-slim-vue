<?php

namespace App\dto;

use Dto\Dto;

/**
 * Class LogConfigurationDto
 *
 * @package App\Dto
 * @property Dto $logDriver
 * @property Dto $options
 */
class LogConfigurationDto extends Dto {

    protected $schema = [
        'type' => 'object',
        'properties' => [
            'logDriver' => ['type' => 'string'],
            'options' => [
                'type' => 'object',
                'properties' => [
                    'awslogs-group' => ['type' => 'string'],
                    'awslogs-region' => ['type' => 'string'],
                    'awslogs-stream-prefix' => ['type' => 'string'],
                ]
            ],
        ],
        'default' => [
            'logDriver' => 'awslogs',
            'options' => [
                'awslogs-group' => '/ecs/deployments',
                'awslogs-region' => '',
                'awslogs-stream-prefix' => 'ecs'
            ]
        ],
    ];
}