<?php

declare(strict_types=1);

namespace Cluster\Provider\Organisation;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Organisation\Type;
use Laminas\Cache\Storage\Adapter\Redis;
use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'organisation_type',
    description: 'Organisation type information',
    content: new OA\JsonContent(ref: '#/components/schemas/organisation_type')
)]
class TypeProvider implements ProviderInterface
{
    public function __construct(private readonly Redis $cache)
    {
    }

    #[OA\Schema(
        schema: 'organisation_type',
        title: 'Organiation type',
        description: 'Information about an organisation type',
        properties: [
            new OA\Property(
                property: 'id',
                description: 'Type ID',
                type: 'integer',
                example: 1
            ),
            new OA\Property(
                property: 'type',
                description: 'Type name',
                type: 'string',
                example: 'Large Industry'
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
                'id'   => $type->getId(),
                'type' => $type->getType(),
            ];

            $this->cache->setItem(key: $cacheKey, value: $typeData);
        }

        return $typeData;
    }
}
