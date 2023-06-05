<?php

declare(strict_types=1);

namespace Cluster\Provider\Version;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Version\Type;
use Laminas\Cache\Storage\Adapter\Redis;
use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'project_version_type',
    description: 'Version type information',
    content: new OA\JsonContent(ref: '#/components/schemas/project_version_type')
)]
class TypeProvider implements ProviderInterface
{
    public function __construct(private readonly Redis $cache)
    {
    }

    #[OA\Schema(
        schema: 'project_version_type',
        title: 'Project version type',
        description: 'Information about a project version type',
        properties: [
            new OA\Property(
                property: 'id',
                description: 'Project Version type ID',
                type: 'integer',
                example: 1
            ),
            new OA\Property(
                property: 'type',
                description: 'Project version type',
                type: 'string',
                example: 'FPP'
            ),
            new OA\Property(
                property: 'description',
                description: 'Version type description',
                type: 'string',
                example: 'Full project proposal'
            ),
        ]
    )]
    public function generateArray($entity): array
    {
        /** @var Type $type */
        $type = $entity;

        $cacheKey = $type->getResourceId();

        $typeData = $this->cache->getItem(key: $cacheKey);

        if (!$typeData) {
            $typeData = [
                'id'          => $type->getId(),
                'type'        => $type->getType(),
                'description' => $type->getDescription(),
            ];

            $this->cache->setItem(key: $cacheKey, value: $typeData);
        }

        return $typeData;
    }
}
