<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Cluster\Entity\Cluster;
use Cluster\Entity;
use Doctrine\Common\Cache\RedisCache;

class ClusterProvider
{
    public function __construct(private RedisCache $redisCache)
    {
    }

    public function generateArray(Cluster $cluster): array
    {
        $cacheKey = $cluster->getResourceId();

        $clusterData = $this->redisCache->fetch($cacheKey);

        if (! $clusterData) {
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
