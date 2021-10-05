<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster;

use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ClusterService;
use Cluster\Service\CountryService;
use Cluster\Service\OrganisationService;
use Cluster\Service\ProjectService;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Provider\ProjectProvider::class         => [
            RedisCache::class,
            ProjectService::class
        ],
        Provider\OrganisationProvider::class    => [
            RedisCache::class
        ],
        Provider\Project\PartnerProvider::class => [
            RedisCache::class,
            ProjectProvider::class,
            OrganisationProvider::class
        ],
        Service\CountryService::class           => [
            EntityManager::class
        ],
        Service\ClusterService::class           => [
            EntityManager::class
        ],
        Service\ProjectService::class           => [
            EntityManager::class,
            ClusterService::class
        ],
        Service\OrganisationService::class      => [
            EntityManager::class
        ],
        Service\Project\PartnerService::class   => [
            EntityManager::class,
            CountryService::class,
            OrganisationService::class
        ],
        Service\Project\VersionService::class   => [
            EntityManager::class
        ],
        Service\StatisticsService::class        => [
            EntityManager::class
        ],
    ]
];
