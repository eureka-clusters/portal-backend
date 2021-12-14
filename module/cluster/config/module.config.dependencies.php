<?php

declare(strict_types=1);

namespace Cluster;

use Cluster\Provider\ClusterProvider;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\CountryProvider;
use Cluster\Provider\Organisation\TypeProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\Project\VersionService;
use Cluster\Provider\ContactProvider;
use Cluster\Provider\Project\StatusProvider;
use Cluster\Provider\Project\VersionProvider;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\CountryService;
use Cluster\Service\ClusterService;
use Cluster\Service\ProjectService;
use Cluster\Service\OrganisationService;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        ClusterProvider::class           => [
            RedisCache::class,
        ],
        OrganisationProvider::class      => [
            RedisCache::class,
            CountryProvider::class,
            TypeProvider::class,
        ],
        TypeProvider::class => [
            RedisCache::class,
        ],
        ProjectProvider::class           => [
            RedisCache::class,
            VersionService::class,
            ClusterProvider::class,
            ContactProvider::class,
            StatusProvider::class,
            VersionProvider::class,
            // ,Provider\Project\PartnerProvider::class
        ],
        PartnerProvider::class   => [
            RedisCache::class,
            ProjectProvider::class,
            ContactProvider::class,
            OrganisationProvider::class,
            PartnerService::class,
        ],
        StatusProvider::class    => [
            RedisCache::class,
        ],
        VersionProvider::class   => [
            RedisCache::class,
            Provider\Version\TypeProvider::class,
            Provider\Version\StatusProvider::class,
        ],
        Provider\Version\StatusProvider::class    => [
            RedisCache::class,
        ],
        Provider\Version\TypeProvider::class      => [
            RedisCache::class,
        ],
        CountryProvider::class           => [
            RedisCache::class,
        ],
        CountryService::class             => [
            EntityManager::class,
        ],
        ClusterService::class             => [
            EntityManager::class,
        ],
        ProjectService::class             => [
            EntityManager::class,
            ClusterService::class,
        ],
        OrganisationService::class        => [
            EntityManager::class,
        ],
        PartnerService::class     => [
            EntityManager::class,
            CountryService::class,
            OrganisationService::class,
        ],
        VersionService::class     => [
            EntityManager::class,
        ],
    ],
];
