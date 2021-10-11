<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Cluster\Entity;
use Cluster\Provider\Version\TypeProvider;
use Doctrine\Common\Cache\RedisCache;

/**
 *
 */
class VersionProvider
{
    private RedisCache                               $redisCache;
    private TypeProvider                             $versionTypeProvider;
    private \Cluster\Provider\Version\StatusProvider $versionStatusProvider;

    public function __construct(
        RedisCache $redisCache,
        TypeProvider $versionTypeProvider,
        \Cluster\Provider\Version\StatusProvider $versionStatusProvider
    ) {
        $this->redisCache            = $redisCache;
        $this->versionTypeProvider   = $versionTypeProvider;
        $this->versionStatusProvider = $versionStatusProvider;
    }

    public function generateArray(Entity\Project\Version $version): array
    {
        $cacheKey = $version->getResourceId();

        $versionData = $this->redisCache->fetch($cacheKey);

        if (!$versionData) {
            $versionData = [
                'id'     => $version->getId(),
                'type'   => $this->versionTypeProvider->generateArray($version->getType()),
                'status' => $this->versionStatusProvider->generateArray($version->getStatus())
            ];

            $this->redisCache->save($cacheKey, $versionData);
        }

        return $versionData;
    }
}
