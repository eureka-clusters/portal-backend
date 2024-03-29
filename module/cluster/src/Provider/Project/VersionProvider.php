<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Version;
use Cluster\Provider\Version\StatusProvider;
use Cluster\Provider\Version\TypeProvider;
use Cluster\Service\Project\VersionService;
use DateTimeInterface;
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
        private readonly Redis          $cache,
        private readonly VersionService $versionService,
        private readonly TypeProvider   $versionTypeProvider,
        private readonly StatusProvider $versionStatusProvider
    )
    {
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
            new OA\Property(
                property: 'dateSubmitted',
                description: 'Date when the version was submitted by the project',
                type: 'string',
                format: 'date-time',
                example: '2023-01-01T00:00:00+00:00'
            ),
            new OA\Property(
                property: 'isLatestVersionAndIsFPP',
                description: 'Boolean value to trigger if this version is the same as the FPP (and can be ignored)',
                type: 'boolean',
                example: false
            ),
            new OA\Property(
                property: 'effort',
                description: 'Total effort in this version in PY',
                type: 'float',
                example: 12.3
            ),
            new OA\Property(
                property: 'costs',
                description: 'Total effort in this version in Euro',
                type: 'float',
                example: 3_400_300
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

            //We need to know if the latest version is FPP or not (or CR)
            $isLatestVersionAndIsFPP = $this->versionService->isLatestVersionAndIsFPP(version: $version);

            $versionData = [
                'id'                      => $version->getId(),
                'type'                    => $this->versionTypeProvider->generateArray(entity: $version->getType()),
                'status'                  => $this->versionStatusProvider->generateArray(entity: $version->getStatus()),
                'dateSubmitted'           => $version->isSubmitted() ? $version->getSubmissionDate()->format(DateTimeInterface::ATOM) : null,
                'isLatestVersionAndIsFPP' => $isLatestVersionAndIsFPP,
                'effort'                  => $version->getEffort(),
                'costs'                   => $version->getCosts(),
            ];

            $this->cache->setItem(key: $cacheKey, value: $versionData);
        }

        return $versionData;
    }
}
