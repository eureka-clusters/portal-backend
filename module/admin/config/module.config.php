<?php

declare(strict_types=1);

namespace Admin;

use Admin\Controller\OAuth2\ClientController;
use Admin\Controller\OAuth2\ScopeController;
use Admin\Controller\RoleController;
use Admin\Controller\UserController;
use Admin\Provider\UserProvider;
use Admin\Service\AdminService;
use Admin\Service\ApiService;
use Admin\Service\UserService;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\Timestampable\TimestampableListener;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;

$config = [
    'service_manager' => [
        'invokables' => [
            UserProvider::class,
            \Admin\Controller\AdminController::class
        ],
        'factories' => [
            AdminService::class => ConfigAbstractFactory::class,
            ApiService::class => ConfigAbstractFactory::class,
            UserService::class => ConfigAbstractFactory::class,
            RoleController::class => ConfigAbstractFactory::class,
            UserController::class => ConfigAbstractFactory::class,
            ScopeController::class => ConfigAbstractFactory::class,
            ClientController::class => ConfigAbstractFactory::class,

        ],
    ],
    'view_manager' => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'doctrine' => [
        'driver' => [
            'admin_attribute_driver' => [
                'class' => AttributeDriver::class,
                'paths' => [
                    __DIR__ . '/../src/Entity/',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Admin\Entity' => 'admin_attribute_driver',
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
