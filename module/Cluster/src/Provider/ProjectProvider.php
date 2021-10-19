<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Provider;

use Cluster\Entity;
use Cluster\Provider\Project\StatusProvider;
use Cluster\Provider\Project\VersionProvider;
use Cluster\Service\Project\VersionService;
use Doctrine\Common\Cache\RedisCache;

/**
 *
 */
class ProjectProvider
{
    private RedisCache      $redisCache;
    private VersionService  $versionService;
    private ClusterProvider $clusterProvider;
    private StatusProvider  $projectStatusProvider;
    private VersionProvider $versionProvider;

    public function __construct(
        RedisCache $redisCache,
        VersionService $versionService,
        ClusterProvider $clusterProvider,
        StatusProvider $projectStatusProvider,
        VersionProvider $versionProvider
    ) {
        $this->redisCache            = $redisCache;
        $this->versionService        = $versionService;
        $this->clusterProvider       = $clusterProvider;
        $this->projectStatusProvider = $projectStatusProvider;
        $this->versionProvider       = $versionProvider;
    }

    public function generateArray(Entity\Project $project): array
    {
        $cacheKey = $project->getIdentifier();

        $projectData = $this->redisCache->fetch($cacheKey);

        if (!$projectData) {
            $projectData = [
                'identifier'               => $project->getIdentifier(),
                'number'                   => $project->getNumber(),
                'name'                     => $project->getName(),
                'title'                    => $project->getTitle(),
                'description'              => $project->getDescription(),
                'technicalArea'            => $project->getTechnicalArea(),
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
                'labelDate'                => $project->getLabelDate()->format(\DateTimeInterface::ATOM),
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
