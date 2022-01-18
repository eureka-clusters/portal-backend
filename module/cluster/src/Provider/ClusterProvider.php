<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Cluster;
use Laminas\Cache\Storage\Adapter\Redis;

class ClusterProvider implements ProviderInterface
{
    public function __construct(private Redis $cache)
    {
    }

    /**
     * @param Cluster $cluster
     * @return array
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function generateArray($cluster): array
    {
        $cacheKey = $cluster->getResourceId();

        $clusterData = $this->cache->getItem($cacheKey);

        if (!$clusterData) {
            $clusterData = [
                'id'          => $cluster->getId(),
                'name'        => $cluster->getName(),
                'description' => $cluster->getDescription(),
            ];

            $this->cache->setItem($cacheKey, $clusterData);
        }

        return $clusterData;
    }
}
