<?php

declare(strict_types=1);

namespace Admin;

use Admin\Controller\OAuth2\ClientController;
use Admin\Controller\OAuth2\ScopeController;
use Admin\Controller\RoleController;
use Admin\Controller\UserController;
use Admin\Navigation\Invokable\OAuth2\ClientLabel;
use Admin\Navigation\Invokable\OAuth2\ScopeLabel;
use Admin\Navigation\Invokable\RoleLabel;
use Admin\Navigation\Invokable\UserLabel;
use Admin\Provider\UserProvider;
use Admin\Service\AdminService;
use Admin\Service\ApiService;
use Admin\Service\UserService;
use Admin\View\Helper\OAuth2\ClientLink;
use Admin\View\Helper\OAuth2\ScopeLink;
use Admin\View\Helper\RoleLink;
use Admin\View\Helper\UserLink;
use Application\Factory\InvokableFactory;
use Application\View\Factory\LinkHelperFactory;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\Timestampable\TimestampableListener;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;

$config = [
    'controllers' => [
        'invokables' => [
            \Admin\Controller\AdminController::class
        ],
        'factories' => [
            RoleController::class => ConfigAbstractFactory::class,
            UserController::class => ConfigAbstractFactory::class,
            ScopeController::class => ConfigAbstractFactory::class,
            ClientController::class => ConfigAbstractFactory::class,

        ],
    ],
    'service_manager' => [
        'invokables' => [
            UserProvider::class,
        ],
        'factories' => [
            AdminService::class => ConfigAbstractFactory::class,
            ApiService::class => ConfigAbstractFactory::class,
            UserService::class => ConfigAbstractFactory::class,
            RoleLabel::class => InvokableFactory::class,
            UserLabel::class => InvokableFactory::class,
            ClientLabel::class => InvokableFactory::class,
            ScopeLabel::class => InvokableFactory::class,
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'roleLink' => RoleLink::class,
            'userLink' => UserLink::class,
            'oauth2clientlink' => ClientLink::class,
            'oauth2scopelink' => ScopeLink::class,

        ],
        'factories' => [
            RoleLink::class => LinkHelperFactory::class,
            UserLink::class => LinkHelperFactory::class,
            ClientLink::class => LinkHelperFactory::class,
            ScopeLink::class => LinkHelperFactory::class,

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

foreach (Glob::glob(pattern: __DIR__ . '/module.config.{,*}.php', flags: Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge(a: $config, b: include $file);
}

return $config;
