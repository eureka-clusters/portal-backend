<?php

declare(strict_types=1);

namespace Cluster;

use Cluster\Provider\ClusterProvider;
use Cluster\Provider\ContactProvider;
use Cluster\Provider\CountryProvider;
use Cluster\Provider\Organisation\TypeProvider;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\Project\PartnerYearProvider;
use Cluster\Provider\Project\StatusProvider;
use Cluster\Provider\Project\VersionProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ClusterService;
use Cluster\Service\CountryService;
use Cluster\Service\OrganisationService;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\Project\VersionService;
use Cluster\Service\ProjectService;
use Doctrine\ORM\EntityManager;
use Laminas\Cache\Storage\Adapter\Redis;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        ClusterProvider::class                 => [
            Redis::class,
        ],
        OrganisationProvider::class            => [
            Redis::class,
            CountryProvider::class,
            TypeProvider::class,
        ],
        TypeProvider::class                    => [
            Redis::class,
        ],
        ProjectProvider::class                 => [
            Redis::class,
            ProjectService::class,
            VersionService::class,
            ClusterProvider::class,
            ContactProvider::class,
            StatusProvider::class,
            VersionProvider::class,
            // ,Provider\Project\PartnerProvider::class
        ],
        PartnerProvider::class                 => [
            Redis::class,
            ProjectProvider::class,
            ContactProvider::class,
            OrganisationProvider::class,
        ],
        PartnerYearProvider::class             => [
            Redis::class,
            ProjectProvider::class,
            ContactProvider::class,
            OrganisationProvider::class,
        ],
        StatusProvider::class                  => [
            Redis::class,
        ],
        VersionProvider::class                 => [
            Redis::class,
            Provider\Version\TypeProvider::class,
            Provider\Version\StatusProvider::class,
        ],
        Provider\Version\StatusProvider::class => [
            Redis::class,
        ],
        Provider\Version\TypeProvider::class   => [
            Redis::class,
        ],
        CountryProvider::class                 => [
            Redis::class,
        ],
        CountryService::class                  => [
            EntityManager::class,
        ],
        ClusterService::class                  => [
            EntityManager::class,
        ],
        ProjectService::class                  => [
            EntityManager::class,
            ClusterService::class,
        ],
        OrganisationService::class             => [
            EntityManager::class,
        ],
        PartnerService::class                  => [
            EntityManager::class,
            CountryService::class,
            OrganisationService::class,
        ],
        VersionService::class                  => [
            EntityManager::class,
        ],
    ],
];
