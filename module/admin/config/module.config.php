<?php

declare(strict_types=1);

namespace Admin;

use Admin\Controller\AdminController;
use Admin\Controller\CacheController;
use Admin\Controller\OAuth2\ClientController;
use Admin\Controller\OAuth2\ScopeController;
use Admin\Controller\OAuth2\ServiceController;
use Admin\Controller\RoleController;
use Admin\Controller\UserController;
use Admin\Navigation\Invokable\OAuth2\ClientLabel;
use Admin\Navigation\Invokable\OAuth2\ScopeLabel;
use Admin\Navigation\Invokable\OAuth2\ServiceLabel;
use Admin\Navigation\Invokable\RoleLabel;
use Admin\Navigation\Invokable\UserLabel;
use Admin\Provider\UserProvider;
use Admin\Service\AdminService;
use Admin\Service\ApiService;
use Admin\Service\UserService;
use Admin\View\Helper\OAuth2\ClientLink;
use Admin\View\Helper\OAuth2\ScopeLink;
use Admin\View\Helper\OAuth2\ServiceLink;
use Admin\View\Helper\RoleLink;
use Admin\View\Helper\UserLink;
use Api\InputFilter\OAuth\ServiceFilter;
use Application\Factory\InputFilterFactory;
use Application\Factory\InvokableFactory;
use Application\View\Factory\LinkHelperFactory;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;

$config = [
    'controllers'     => [
        'invokables' => [
            AdminController::class,
        ],
        'factories'  => [
            RoleController::class    => ConfigAbstractFactory::class,
            UserController::class    => ConfigAbstractFactory::class,
            ScopeController::class   => ConfigAbstractFactory::class,
            ServiceController::class => ConfigAbstractFactory::class,
            ClientController::class  => ConfigAbstractFactory::class,
            CacheController::class   => ConfigAbstractFactory::class,
        ],
    ],
    'service_manager' => [
        'invokables' => [
            UserProvider::class,
        ],
        'factories'  => [
            AdminService::class  => ConfigAbstractFactory::class,
            ApiService::class    => ConfigAbstractFactory::class,
            UserService::class   => ConfigAbstractFactory::class,
            ServiceFilter::class => InputFilterFactory::class, //Has to be in Api namespace
            RoleLabel::class     => InvokableFactory::class,
            UserLabel::class     => InvokableFactory::class,
            ServiceLabel::class  => InvokableFactory::class,
            ClientLabel::class   => InvokableFactory::class,
            ScopeLabel::class    => InvokableFactory::class,
        ],
    ],
    'view_helpers'    => [
        'aliases'   => [
            'roleLink'          => RoleLink::class,
            'userLink'          => UserLink::class,
            'oauth2ClientLink'  => ClientLink::class,
            'oauth2ScopeLink'   => ScopeLink::class,
            'oauth2ServiceLink' => ServiceLink::class,
        ],
        'factories' => [
            RoleLink::class    => LinkHelperFactory::class,
            UserLink::class    => LinkHelperFactory::class,
            ClientLink::class  => LinkHelperFactory::class,
            ScopeLink::class   => LinkHelperFactory::class,
            ServiceLink::class => LinkHelperFactory::class,
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'doctrine'        => [
        'driver' => [
            'admin_attribute_driver' => [
                'class' => AttributeDriver::class,
                'paths' => [
                    __DIR__ . '/../src/Entity/',
                ],
            ],
            'orm_default'            => [
                'drivers' => [
                    'Admin\Entity' => 'admin_attribute_driver',
                ],
            ],
        ],
    ],
];

foreach (Glob::glob(pattern: __DIR__ . '/module.config.{,*}.php', flags: Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge(a: $config, b: include $file);
}

return $config;
