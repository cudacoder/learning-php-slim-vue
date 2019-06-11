<?php

namespace App\dto;

use Dto\Dto;

/**
 * Class EcrObject
 * @package Appli\Dto
 * @property Dto $hostPort
 * @property Dto $protocol
 * @property Dto $containerPort
 */
class ContainerPortMappingsDto extends Dto {

    protected $schema = [
        'type' => 'object',
        'properties' => [
            'hostPort' => ['type' => 'integer'],
            'protocol' => ['type' => ['string']],
            'containerPort' => ['type' => 'integer'],
        ]
    ];
}