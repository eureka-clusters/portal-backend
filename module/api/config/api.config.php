<?php

declare(strict_types=1);

namespace Api;

use Api\V1\Rest;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

use function _;
use function array_merge_recursive;

$config = [
    'router'          => [
        'routes' => [
            'api' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/api',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'navigation'      => [
        'admin' => [
            'tools' => [
                'pages' => [
                    'api' => [
                        'order' => 110,
                        'label' => _('txt-api-tools'),
                        'route' => 'api',
                    ],
                ],
            ],
        ],
    ],
    'doctrine'        => [
        'driver' => [
            'api_annotation_driver' => [
                'class' => AnnotationDriver::class,
                'paths' => [
                    0 => __DIR__ . '/../src/Entity/',
                ],
            ],
            'orm_default'           => [
                'drivers' => [
                    'Api\\Entity' => 'api_annotation_driver',
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\OAuthService::class                             => ConfigAbstractFactory::class,
            Options\ModuleOptions::class                            => Factory\ModuleOptionsFactory::class,
            Rest\UserResource\MeListener::class                     => ConfigAbstractFactory::class,
            Rest\ListResource\ProjectListener::class                => ConfigAbstractFactory::class,
            Rest\ListResource\OrganisationListener::class           => ConfigAbstractFactory::class,
            Rest\ListResource\PartnerListener::class                => ConfigAbstractFactory::class,
            Rest\ViewResource\ProjectListener::class                => ConfigAbstractFactory::class,
            Rest\ViewResource\OrganisationListener::class           => ConfigAbstractFactory::class,
            Rest\ViewResource\PartnerListener::class                => ConfigAbstractFactory::class,
            Rest\UpdateResource\ProjectListener::class              => ConfigAbstractFactory::class,
            Rest\StatisticsResource\Results\ProjectListener::class  => ConfigAbstractFactory::class,
            Rest\StatisticsResource\Results\PartnerListener::class  => ConfigAbstractFactory::class,
            Rest\StatisticsResource\Facets\ProjectListener::class   => ConfigAbstractFactory::class,
            Rest\StatisticsResource\Facets\PartnerListener::class   => ConfigAbstractFactory::class,
            Rest\StatisticsResource\Download\ProjectListener::class => ConfigAbstractFactory::class,
            Rest\StatisticsResource\Download\PartnerListener::class => ConfigAbstractFactory::class,
        ],
    ],
];

return array_merge_recursive(
    $config,
    include 'module.config.php'
);
