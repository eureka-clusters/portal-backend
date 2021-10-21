<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Cluster\Entity;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\ProjectService;
use Doctrine\Common\Cache\RedisCache;

/**
 *
 */
class PartnerProvider
{
    private RedisCache           $redisCache;
    private ProjectProvider      $projectProvider;
    private OrganisationProvider $organisationProvider;
    private ProjectService       $projectService;
    private PartnerService       $partnerService;

    public function __construct(
        RedisCache $redisCache,
        ProjectProvider $projectProvider,
        OrganisationProvider $organisationProvider,
        ProjectService $projectService,
        PartnerService $partnerService
    ) {
        $this->redisCache           = $redisCache;
        $this->projectProvider      = $projectProvider;
        $this->organisationProvider = $organisationProvider;
        $this->projectService       = $projectService;
        $this->partnerService       = $partnerService;
    }

    public function generateArray(Entity\Project\Partner $partner): array
    {
        $cacheKey    = $partner->getResourceId();
        $partnerData = $this->redisCache->fetch($cacheKey);

        if (!$partnerData) {
            $partnerData = [
                'id'                  => $partner->getId(),
                'project'             => $this->projectProvider->generateArray($partner->getProject()),
                'isActive'            => $partner->isActive(),
                'isSelfFunded'        => $partner->isSelfFunded(),
                'isCoordinator'       => $partner->isCoordinator(),
                'technicalContact'    => $partner->getTechnicalContact(),
                'organisation'        => $this->organisationProvider->generateArray($partner->getOrganisation()),
                'latestVersionCosts'  => $this->partnerService->parseTotalCostsByPartnerAndLatestProjectVersion(
                    $partner,
                    $partner->getProject()->getLatestVersion()
                ),
                'latestVersionEffort' => $this->partnerService->parseTotalEffortByPartnerAndLatestProjectVersion(
                    $partner,
                    $partner->getProject()->getLatestVersion()
                ),
            ];

            $this->redisCache->save($cacheKey, $partnerData);
        }

        return $partnerData;
    }
}
