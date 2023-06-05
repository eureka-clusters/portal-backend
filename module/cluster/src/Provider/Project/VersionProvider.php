<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Version;
use Cluster\Provider\Version\StatusProvider;
use Cluster\Provider\Version\TypeProvider;
use Laminas\Cache\Storage\Adapter\Redis;
use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'project_version',
    description: 'Version information',
    content: new OA\JsonContent(ref: '#/components/schemas/project_version')
)]
class VersionProvider implements ProviderInterface
{
    public function __construct(
        private readonly Redis $cache,
        private readonly TypeProvider $versionTypeProvider,
        private readonly StatusProvider $versionStatusProvider
    ) {
    }

    #[OA\Schema(
        schema: 'project_version',
        title: 'Project version',
        description: 'Information about a project version',
        properties: [
            new OA\Property(
                property: 'id',
                description: 'Version ID',
                type: 'integer',
                example: 1
            ),
            new OA\Property(
                property: 'type',
                ref: '#/components/schemas/project_version_type'
            ),
            new OA\Property(
                property: 'status',
                ref: '#/components/schemas/project_version_status'
            ),
        ]
    )]
    public function generateArray($entity): array
    {
        /** @var Version $version */
        $version = $entity;

        $cacheKey = $version->getResourceId();

        $versionData = $this->cache->getItem(key: $cacheKey);

        if (!$versionData) {
            $versionData = [
                'id'     => $version->getId(),
                'type'   => $this->versionTypeProvider->generateArray(entity: $version->getType()),
                'status' => $this->versionStatusProvider->generateArray(entity: $version->getStatus()),
            ];

            $this->cache->setItem(key: $cacheKey, value: $versionData);
        }

        return $versionData;
    }
}
