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
use Doctrine\Common\Cache\RedisCache;

/**
 *
 */
class PartnerProvider
{
    private RedisCache           $redisCache;
    private ProjectProvider      $projectProvider;
    private OrganisationProvider $organisationProvider;

    public function __construct(
        RedisCache $redisCache,
        ProjectProvider $projectProvider,
        OrganisationProvider $organisationProvider
    ) {
        $this->redisCache           = $redisCache;
        $this->projectProvider      = $projectProvider;
        $this->organisationProvider = $organisationProvider;
    }

    public function generateArray(Entity\Project\Partner $partner): array
    {
        $cacheKey = 'test';

        //$partnerData = false;//$this->redisCache->fetch($cacheKey);
        $partnerData = [
            'id'            => $partner->getId(),
            'project'       => $this->projectProvider->generateArray($partner->getProject()),
            'isActive'      => $partner->isActive(),
            'isSelfFunded'  => $partner->isSelfFunded(),
            'isCoordinator' => $partner->isCoordinator(),
            'organisation'  => $this->organisationProvider->generateArray($partner->getOrganisation())
        ];

        if (!$partnerData) {
            // $projectData['internal_identifier'] = ITEAOFFICE_HOST . '-' . $project->getId();

            $this->redisCache->save($cacheKey, $partnerData);
        }

        return $partnerData;
    }
}
