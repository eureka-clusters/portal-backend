<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Cluster;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib;

$config = [
    'service_manager' => [
        'factories' => [
            Provider\ClusterProvider::class           => ConfigAbstractFactory::class,
            Provider\OrganisationProvider::class      => ConfigAbstractFactory::class,
            Provider\Organisation\TypeProvider::class => ConfigAbstractFactory::class,
            Provider\ProjectProvider::class           => ConfigAbstractFactory::class,
            Provider\Project\PartnerProvider::class   => ConfigAbstractFactory::class,
            Provider\Project\StatusProvider::class    => ConfigAbstractFactory::class,
            Provider\Project\VersionProvider::class   => ConfigAbstractFactory::class,
            Provider\Version\StatusProvider::class    => ConfigAbstractFactory::class,
            Provider\Version\TypeProvider::class      => ConfigAbstractFactory::class,
            Provider\CountryProvider::class           => ConfigAbstractFactory::class,

            Service\ClusterService::class         => ConfigAbstractFactory::class,
            Service\CountryService::class         => ConfigAbstractFactory::class,
            Service\OrganisationService::class    => ConfigAbstractFactory::class,
            Service\ProjectService::class         => ConfigAbstractFactory::class,
            Service\Project\VersionService::class => ConfigAbstractFactory::class,
            Service\Project\PartnerService::class => ConfigAbstractFactory::class
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
foreach (Stdlib\Glob::glob(__DIR__ . '/module.config.{,*}.php', Stdlib\Glob::GLOB_BRACE) as $file) {
    $config = Stdlib\ArrayUtils::merge($config, include $file);
}
return $config;
