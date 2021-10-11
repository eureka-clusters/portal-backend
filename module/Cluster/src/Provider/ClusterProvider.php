<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Provider;

use Cluster\Entity;
use Doctrine\Common\Cache\RedisCache;

/**
 *
 */
class ClusterProvider
{
    private RedisCache $redisCache;

    public function __construct(RedisCache $redisCache)
    {
        $this->redisCache = $redisCache;
    }

    public function generateArray(Entity\Cluster $cluster): array
    {
        $cacheKey = $cluster->getResourceId();

        $clusterData = $this->redisCache->fetch($cacheKey);

        if (!$clusterData) {
            $clusterData = [
                'id'          => $cluster->getId(),
                'name'        => $cluster->getName(),
                'description' => $cluster->getDescription(),
            ];

            $this->redisCache->save($cacheKey, $clusterData);
        }

        return $clusterData;
    }
}
