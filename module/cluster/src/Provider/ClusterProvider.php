<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Cluster;
use Laminas\Cache\Storage\Adapter\Redis;
use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'cluster',
    description: 'Cluster information',
    content: new OA\JsonContent(ref: '#/components/schemas/cluster')
)]
class ClusterProvider implements ProviderInterface
{
    public function __construct(private readonly Redis $cache)
    {
    }

    #[OA\Schema(
        schema: 'cluster',
        title: 'Cluster',
        description: 'Information about a cluster',
        properties: [
            new OA\Property(
                property: 'id',
                description: 'Cluster ID',
                type: 'integer',
                example: 1
            ),
            new OA\Property(
                property: 'name',
                description: 'Cluster name',
                type: 'string',
                example: 'ITEA'
            ),
            new OA\Property(
                property: 'description',
                description: 'Cluster description',
                type: 'string',
                example: 'ITEA is the Eureka Cluster on software innovation'
            ),
        ]
    )]
    public function generateArray($entity): array
    {
        /** @var Cluster $cluster */
        $cluster = $entity;

        $cacheKey = $cluster->getResourceId();

        $clusterData = $this->cache->getItem(key: $cacheKey);

        if (!$clusterData) {
            $clusterData = [
                'id'          => $cluster->getId(),
                'name'        => $cluster->getName(),
                'description' => $cluster->getDescription(),
            ];

            $this->cache->setItem(key: $cacheKey, value: $clusterData);
        }

        return $clusterData;
    }
}
