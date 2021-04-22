<?php
/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Admin;

use Application\Factory\InputFilterFactory;
use Application\Factory\InvokableFactory;
use Application\View\Factory\LinkHelperFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib;
use League\OAuth2\Client\Provider\GenericProvider;

$config = [
    'controllers'  => [
        'invokables' => [
            \Api\Controller\IndexController::class
        ]
    ],
    'view_manager' => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],

    'view_helpers'    => [

    ],
    'service_manager' => [
        'factories' => [
            Service\AdminService::class         => ConfigAbstractFactory::class,
            Service\ApiService::class           => ConfigAbstractFactory::class,
            Service\UserService::class          => ConfigAbstractFactory::class,
            Service\QueueService::class         => ConfigAbstractFactory::class
        ],
    ],
    'doctrine'        => [
        'driver' => [
            'admin_annotation_driver' => [
                'class' => AnnotationDriver::class,
                'paths' => [
                    __DIR__ . '/../src/Entity/',
                ],
            ],
            'orm_default'             => [
                'drivers' => [
                    'Admin\Entity' => 'admin_annotation_driver',
                ],
            ],
        ],
    ],
];

foreach (Stdlib\Glob::glob(__DIR__ . '/module.config.{,*}.php', Stdlib\Glob::GLOB_BRACE) as $file) {
    $config = Stdlib\ArrayUtils::merge($config, include $file);
}

return $config;
