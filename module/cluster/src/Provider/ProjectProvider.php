<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project;
use Cluster\Provider\Project\Partner\CoordinatorProvider;
use Cluster\Provider\Project\StatusProvider;
use Cluster\Provider\Project\VersionProvider;
use Cluster\Service\Project\VersionService;
use Cluster\Service\ProjectService;
use DateTimeInterface;
use Laminas\Cache\Storage\Adapter\Redis;
use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'project',
    description: 'Project information',
    content: new OA\JsonContent(ref: '#/components/schemas/project')
)]
class ProjectProvider implements ProviderInterface
{
    public function __construct(
        private readonly Redis $cache,
        private readonly ProjectService $projectService,
        private readonly CoordinatorProvider $coordinatorProvider,
        private readonly VersionService $versionService,
        private readonly ClusterProvider $clusterProvider,
        private readonly ContactProvider $contactProvider,
        private readonly StatusProvider $projectStatusProvider,
        private readonly VersionProvider $versionProvider
    ) {
    }

    #[OA\Schema(
        schema: 'project',
        title: 'Project',
        description: 'Information about a project',
        properties: [
            new OA\Property(
                property: 'slug',
                description: 'Project slug',
                type: 'string',
                example: 'project-1'
            ),
            new OA\Property(
                property: 'identifier',
                description: 'Project identifier',
                type: 'string',
                example: 'itea-2022001'
            ),
            new OA\Property(
                property: 'number',
                description: 'Project number',
                type: 'string',
                example: 'P1'
            ),
            new OA\Property(
                property: 'name',
                description: 'Project name',
                type: 'string',
                example: 'Project 1'
            ),
            new OA\Property(
                property: 'title',
                description: 'Project title',
                type: 'string',
                example: 'Project 1'
            ),
            new OA\Property(
                property: 'description',
                description: 'Project description',
                type: 'string',
                example: 'This is the project description',
                nullable: true
            ),
            new OA\Property(
                property: 'technicalArea',
                description: 'Project technical area',
                type: 'string',
                example: 'Smart Energy',
                nullable: true
            ),
            new OA\Property(
                property: 'coordinator',
                ref: '#/components/schemas/project_partner_coordinator',
                description: 'Project coordinator information',
                nullable: true
            ),
            new OA\Property(
                property: 'projectLeader',
                ref: '#/components/schemas/contact',
                description: 'Project leader information'
            ),
            new OA\Property(
                property: 'latestVersion',
                ref: '#/components/schemas/project_version',
                description: 'Latest project version information',
                nullable: true
            ),
            new OA\Property(
                property: 'programme',
                description: 'Programme of the project',
                type: 'string',
                example: 'ITEA',
            ),
            new OA\Property(
                property: 'programmeCall',
                description: 'Programme call of the project',
                type: 'string',
                example: 'ITEA Call 2023',
            ),

            new OA\Property(
                property: 'primaryCluster',
                ref: '#/components/schemas/cluster',
                description: 'Primary cluster'
            ),
            new OA\Property(
                property: 'secondaryCluster',
                ref: '#/components/schemas/cluster',
                description: 'Secondary cluster'
            ),
            new OA\Property(
                property: 'cancelDate',
                description: 'Date when the project was cancelled. Null if the project is not cancelled',
                type: 'string',
                format: 'date-time',
                example: null,
                nullable: true
            ),
            new OA\Property(
                property: 'labelDate',
                description: 'Date when the project was labelled. Null if the project is not labelled',
                type: 'string',
                format: 'date-time',
                example: "2023-01-01T09:00:00+00:00",
                nullable: true
            ),
            new OA\Property(
                property: 'officialStartDate',
                description: 'Official date when the project starts',
                type: 'string',
                format: 'date-time',
                example: "2023-04-01T09:00:00+00:00",
                nullable: true
            ),
            new OA\Property(
                property: 'officialEndDate',
                description: 'Official date when the project ends',
                type: 'string',
                format: 'date-time',
                example: "2026-03-31T09:00:00+00:00",
                nullable: true
            ),
            new OA\Property(
                property: 'duration',
                description: 'Project duration fields',
                properties: [
                    new OA\Property(
                        property: 'years',
                        description: 'Project duration in years',
                        type: 'integer',
                        example: 3
                    ),
                    new OA\Property(
                        property: 'months',
                        description: 'Project duration in months',
                        type: 'integer',
                        example: 36
                    ),
                    new OA\Property(
                        property: 'days',
                        description: 'Project duration in days',
                        type: 'integer',
                        example: 3 * 365
                    ),
                ],
                type: 'object'
            ),
            new OA\Property(
                property: 'status',
                ref: '#/components/schemas/project_status',
                description: 'Project status information'
            ),
            new OA\Property(
                property: 'latestVersionTotalCosts',
                description: 'Total costs of the latest project version (In EUR)',
                type: 'float',
                example: 5_000_000
            ),
            new OA\Property(
                property: 'latestVersionTotalEffort',
                description: 'Total effort of the latest project version (In Person Years)',
                type: 'float',
                example: 82.3
            )
        ]
    )]
    public function generateArray($entity): array
    {
        /** @var Project $project */
        $project = $entity;

        $cacheKey = $project->getResourceId();

        $projectData = $this->cache->getItem(key: $cacheKey);

        if (!$projectData) {
            $projectData = [
                'slug'                     => $project->getSlug(),
                'identifier'               => $project->getIdentifier(),
                'number'                   => $project->getNumber(),
                'name'                     => $project->getName(),
                'title'                    => $project->getTitle(),
                'description'              => $project->getDescription(),
                'technicalArea'            => $project->getTechnicalArea(),
                'coordinator'              => null === $project->getCoordinatorPartner(
                ) ? null : $this->coordinatorProvider->generateArray(entity: $project->getCoordinatorPartner()),
                'projectLeader'            => $this->contactProvider->generateArray(
                    entity: $project->getProjectLeader()
                ),
                'latestVersion'            => null === $project->getLatestVersion(
                ) ? null : $this->versionProvider->generateArray(
                    entity: $project->getLatestVersion()
                ),
                'programme'                => $project->getProgramme(),
                'programmeCall'            => $project->getProgrammeCall(),
                'primaryCluster'           => $this->clusterProvider->generateArray(
                    entity: $project->getPrimaryCluster()
                ),
                'secondaryCluster'         => !$project->hasSecondaryCluster(
                ) ? null : $this->clusterProvider->generateArray(
                    entity: $project->getSecondaryCluster()
                ),
                'cancelDate'               => $project->getCancelDate()?->format(format: DateTimeInterface::ATOM),
                'labelDate'                => $project->getLabelDate()?->format(format: DateTimeInterface::ATOM),
                'officialStartDate'        => $project->getOfficialStartDate()?->format(
                    format: DateTimeInterface::ATOM
                ),
                'officialEndDate'          => $project->getOfficialEndDate()?->format(format: DateTimeInterface::ATOM),
                'duration'                 => [
                    'years'  => $this->projectService->parseDuration(
                        project: $project,
                        type: ProjectService::DURATION_YEAR
                    ),
                    'months' => $this->projectService->parseDuration(
                        project: $project,
                        type: ProjectService::DURATION_MONTH
                    ),
                    'days'   => $this->projectService->parseDuration(
                        project: $project,
                        type: ProjectService::DURATION_DAYS
                    ),
                ],
                'status'                   => $this->projectStatusProvider->generateArray(
                    entity: $project->getStatus()
                ),
                'latestVersionTotalCosts'  => null === $project->getLatestVersion(
                ) ? null : $this->versionService->parseTotalCostsByProjectVersion(
                    projectVersion: $project->getLatestVersion()
                ),
                'latestVersionTotalEffort' => null === $project->getLatestVersion(
                ) ? null : $this->versionService->parseTotalEffortByProjectVersion(
                    projectVersion: $project->getLatestVersion()
                ),
            ];

            $this->cache->setItem(key: $cacheKey, value: $projectData);
        }

        return $projectData;
    }
}
