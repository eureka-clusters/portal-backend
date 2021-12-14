<?php

declare(strict_types=1);

namespace Api;

use Api\Service\OAuthService;
use Api\V1\Rest;
use Api\V1\Rest\ListResource\OrganisationListener;
use Api\V1\Rest\ListResource\PartnerListener;
use Api\V1\Rest\ListResource\ProjectListener;
use Api\V1\Rest\UserResource\MeListener;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

use function array_merge_recursive;

$config = [
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
            OAuthService::class                                     => ConfigAbstractFactory::class,
            MeListener::class                                       => ConfigAbstractFactory::class,
            ProjectListener::class                                  => ConfigAbstractFactory::class,
            OrganisationListener::class                             => ConfigAbstractFactory::class,
            PartnerListener::class                                  => ConfigAbstractFactory::class,
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
