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

use Cluster\Provider\PartnerProvider;
use Cluster\Service\PartnerService;
use Cluster\Service\ProjectService;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Provider\ProjectProvider::class  => [
            RedisCache::class,
            ProjectService::class,
            PartnerProvider::class
        ],
        Provider\PartnerProvider::class  => [
            RedisCache::class,
            PartnerService::class
        ],
        Service\ClusterService::class    => [
            EntityManager::class
        ],
        Service\PartnerService::class    => [
            EntityManager::class
        ],
        Service\ProjectService::class    => [
            EntityManager::class
        ],
        Service\StatisticsService::class => [
            EntityManager::class
        ],
    ]
];
