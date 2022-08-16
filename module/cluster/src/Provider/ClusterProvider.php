<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Cluster;
use Laminas\Cache\Exception\ExceptionInterface;
use Laminas\Cache\Storage\Adapter\Redis;

class ClusterProvider implements ProviderInterface
{
    public function __construct(private readonly Redis $cache)
    {
    }

    /**
     * @param Cluster $cluster
     * @throws ExceptionInterface
     */
    public function generateArray($cluster): array
    {
        $cacheKey = $cluster->getResourceId();

        $clusterData = $this->cache->getItem($cacheKey);

        if (!$clusterData) {
            $clusterData = [
                'id' => $cluster->getId(),
                'name' => $cluster->getName(),
                'description' => $cluster->getDescription(),
            ];

            $this->cache->setItem($cacheKey, $clusterData);
        }

        return $clusterData;
    }
}
