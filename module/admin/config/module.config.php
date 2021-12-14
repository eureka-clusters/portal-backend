<?php

declare(strict_types=1);

namespace Admin;

use Admin\Provider\UserProvider;
use Admin\Service\AdminService;
use Admin\Service\ApiService;
use Admin\Service\UserService;
use Laminas\Stdlib\Glob;
use Laminas\Stdlib\ArrayUtils;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\Timestampable\TimestampableListener;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib;

$config = [
    'service_manager' => [
        'invokables' => [
            UserProvider::class,
        ],
        'factories'  => [
            AdminService::class => ConfigAbstractFactory::class,
            ApiService::class   => ConfigAbstractFactory::class,
            UserService::class  => ConfigAbstractFactory::class,
        ],
    ],
    'doctrine'        => [
        'driver'       => [
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
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    TimestampableListener::class,
                    SluggableListener::class,
                ],
            ],
        ],
    ],
];

foreach (Glob::glob(__DIR__ . '/module.config.{,*}.php', Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge($config, include $file);
}

return $config;
