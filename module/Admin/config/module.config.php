<?php

declare(strict_types=1);

namespace Admin;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\Timestampable\TimestampableListener;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib;

$config = [
    'service_manager' => [
        'invokables' => [
            Provider\UserProvider::class,
        ],
        'factories'  => [
            Service\AdminService::class => ConfigAbstractFactory::class,
            Service\ApiService::class   => ConfigAbstractFactory::class,
            Service\UserService::class  => ConfigAbstractFactory::class,
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

foreach (Stdlib\Glob::glob(__DIR__ . '/module.config.{,*}.php', Stdlib\Glob::GLOB_BRACE) as $file) {
    $config = Stdlib\ArrayUtils::merge($config, include $file);
}

return $config;
