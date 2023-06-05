<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Status;
use Laminas\Cache\Storage\Adapter\Redis;
use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'project_status',
    description: 'Project status information',
    content: new OA\JsonContent(ref: '#/components/schemas/project_status')
)]
class StatusProvider implements ProviderInterface
{
    public function __construct(private readonly Redis $cache)
    {
    }

    #[OA\Schema(
        schema: 'project_status',
        title: 'Project status',
        description: 'Information about a project status',
        properties: [
            new OA\Property(
                property: 'id',
                description: 'Status ID',
                type: 'integer',
                example: 1
            ),
            new OA\Property(
                property: 'status',
                description: 'Status',
                type: 'string',
                example: 'Labeled'
            ),
        ]
    )]
    public function generateArray($entity): array
    {
        /** @var Status $status */
        $status = $entity;

        $cacheKey = $status->getResourceId();

        $statusData = $this->cache->getItem(key: $cacheKey);

        if (!$statusData) {
            $statusData = [
                'id'     => $status->getId(),
                'status' => $status->getStatus(),
            ];

            $this->cache->setItem(key: $cacheKey, value: $statusData);
        }

        return $statusData;
    }
}
