<?php

declare(strict_types=1);

namespace Cluster\Provider;

use Cluster\Entity\Project;
use Cluster\Entity;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\Project\StatusProvider;
use Cluster\Provider\Project\VersionProvider;
use Cluster\Service\Project\VersionService;
use DateTimeInterface;
use Doctrine\Common\Cache\RedisCache;

class ProjectProvider
{
    public function __construct(
        private RedisCache $redisCache,
        private VersionService $versionService,
        private ClusterProvider $clusterProvider,
        private ContactProvider $contactProvider,
        private StatusProvider $projectStatusProvider,
        private VersionProvider $versionProvider
    ) {
    }

    public function generateArray(Project $project): array
    {
        $cacheKey = $project->getResourceId();

        $projectData = $this->redisCache->fetch($cacheKey);

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
                ) ? null : PartnerProvider::parseCoordinatorArray($project->getCoordinatorPartner()),
                'projectLeader'            => $this->contactProvider->generateArray($project->getProjectLeader()),
                'latestVersion'            => null === $project->getLatestVersion(
                ) ? null : $this->versionProvider->generateArray(
                    $project->getLatestVersion()
                ),
                'programme'                => $project->getProgramme(),
                'programmeCall'            => $project->getProgrammeCall(),
                'primaryCluster'           => $this->clusterProvider->generateArray($project->getPrimaryCluster()),
                'secondaryCluster'         => !$project->hasSecondaryCluster(
                ) ? null : $this->clusterProvider->generateArray(
                    $project->getSecondaryCluster()
                ),
                'labelDate'                => $project->getLabelDate()->format(DateTimeInterface::ATOM),
                'status'                   => $this->projectStatusProvider->generateArray($project->getStatus()),
                'latestVersionTotalCosts'  => $this->versionService->parseTotalCostsByProjectVersion(
                    $project->getLatestVersion()
                ),
                'latestVersionTotalEffort' => $this->versionService->parseTotalEffortByProjectVersion(
                    $project->getLatestVersion()
                ),
            ];

            $this->redisCache->save($cacheKey, $projectData);
        }

        return $projectData;
    }
}
