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
use Doctrine\Common\Cache\RedisCache;

/**
 *
 */
class ProjectProvider
{
    private RedisCache $redisCache;

    public function __construct(RedisCache $redisCache)
    {
        $this->redisCache = $redisCache;
    }

    public function generateArray(Entity\Project $project): array
    {
        $cacheKey = $project->getIdentifier();

        $projectData = $this->redisCache->fetch($cacheKey);

        if (true || !$projectData) {
            $projectData = [
                'identifier'     => $project->getIdentifier(),
                'number'         => $project->getNumber(),
                'name'           => $project->getName(),
                'title'          => $project->getTitle(),
                'description'    => $project->getDescription(),
                'technicalArea'  => $project->getTechnicalArea(),
                'latestVersion'  => null === $project->getLatestVersion() ? null : $project->getLatestVersion(
                )->getType()->getType(),
                'programme'      => $project->getProgramme(),
                'programmeCall'  => $project->getProgrammeCall(),
                'primaryCluster' => $project->getPrimaryCluster()->getName(),
                'labelDate'      => $project->getLabelDate()->format(\DateTimeInterface::ATOM),
                'status'         => $project->getStatus()->getStatus(),
            ];


            $this->redisCache->save($cacheKey, $projectData);
        }

        return $projectData;
    }
}
