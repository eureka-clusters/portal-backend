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

use Cluster\Provider\ContactProvider;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Provider\ClusterProvider::class           => [
            RedisCache::class
        ],
        Provider\OrganisationProvider::class      => [
            RedisCache::class,
            Provider\CountryProvider::class,
            Provider\Organisation\TypeProvider::class
        ],
        Provider\Organisation\TypeProvider::class => [
            RedisCache::class
        ],
        Provider\ProjectProvider::class           => [
            RedisCache::class,
            Service\Project\VersionService::class,
            Provider\ClusterProvider::class,
            Provider\ContactProvider::class,
            Provider\Project\StatusProvider::class,
            Provider\Project\VersionProvider::class
            // ,Provider\Project\PartnerProvider::class
        ],
        Provider\Project\PartnerProvider::class   => [
            RedisCache::class,
            Provider\ProjectProvider::class,
            Provider\ContactProvider::class,
            Provider\OrganisationProvider::class,
            Service\Project\PartnerService::class
        ],
        Provider\Project\StatusProvider::class    => [
            RedisCache::class
        ],
        Provider\Project\VersionProvider::class   => [
            RedisCache::class,
            Provider\Version\TypeProvider::class,
            Provider\Version\StatusProvider::class,
        ],
        Provider\Version\StatusProvider::class    => [
            RedisCache::class
        ],
        Provider\Version\TypeProvider::class      => [
            RedisCache::class
        ],
        Provider\CountryProvider::class           => [
            RedisCache::class
        ],
        Service\CountryService::class             => [
            EntityManager::class
        ],
        Service\ClusterService::class             => [
            EntityManager::class
        ],
        Service\ProjectService::class             => [
            EntityManager::class,
            Service\ClusterService::class
        ],
        Service\OrganisationService::class        => [
            EntityManager::class
        ],
        Service\Project\PartnerService::class     => [
            EntityManager::class,
            Service\CountryService::class,
            Service\OrganisationService::class
        ],
        Service\Project\VersionService::class     => [
            EntityManager::class
        ],
    ]
];
