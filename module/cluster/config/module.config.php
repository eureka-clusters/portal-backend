<?php

declare(strict_types=1);

namespace Cluster;

use Cluster\Provider\ClusterProvider;
use Cluster\Provider\ContactProvider;
use Cluster\Provider\CountryProvider;
use Cluster\Provider\Organisation\TypeProvider;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\Project\StatusProvider;
use Cluster\Provider\Project\VersionProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ClusterService;
use Cluster\Service\CountryService;
use Cluster\Service\OrganisationService;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\Project\VersionService;
use Cluster\Service\ProjectService;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;

$config = [
    'service_manager' => [
        'factories'  => [
            ClusterProvider::class                 => ConfigAbstractFactory::class,
            OrganisationProvider::class            => ConfigAbstractFactory::class,
            TypeProvider::class                    => ConfigAbstractFactory::class,
            ProjectProvider::class                 => ConfigAbstractFactory::class,
            PartnerProvider::class                 => ConfigAbstractFactory::class,
            StatusProvider::class                  => ConfigAbstractFactory::class,
            VersionProvider::class                 => ConfigAbstractFactory::class,
            Provider\Version\StatusProvider::class => ConfigAbstractFactory::class,
            Provider\Version\TypeProvider::class   => ConfigAbstractFactory::class,
            CountryProvider::class                 => ConfigAbstractFactory::class,
            ClusterService::class                  => ConfigAbstractFactory::class,
            CountryService::class                  => ConfigAbstractFactory::class,
            OrganisationService::class             => ConfigAbstractFactory::class,
            ProjectService::class                  => ConfigAbstractFactory::class,
            VersionService::class                  => ConfigAbstractFactory::class,
            PartnerService::class                  => ConfigAbstractFactory::class,
        ],
        'invokables' => [
            ContactProvider::class,
        ],
    ],
    'doctrine'        => [
        'driver' => [
            'cluster_annotation_driver' => [
                'class' => AnnotationDriver::class,
                'paths' => [__DIR__ . '/../src/Entity/'],
            ],
            'orm_default'               => [
                'drivers' => [
                    'Cluster\Entity' => 'cluster_annotation_driver',
                ],
            ],
        ],
    ],
];
foreach (Glob::glob(__DIR__ . '/module.config.{,*}.php', Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge($config, include $file);
}
return $config;
