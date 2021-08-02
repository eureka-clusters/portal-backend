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
use Cluster\Service\ProjectService;
use Doctrine\Common\Cache\RedisCache;

/**
 *
 */
class ProjectProvider
{
    private RedisCache      $redisCache;
    private ProjectService  $projectService;
    private PartnerProvider $partnerProvider;

    public function __construct(
        RedisCache $redisCache,
        ProjectService $projectService,
        PartnerProvider $partnerProvider
    ) {
        $this->redisCache      = $redisCache;
        $this->projectService  = $projectService;
        $this->partnerProvider = $partnerProvider;
    }

    public function generateArray(Entity\Statistics\Partner $partner): array
    {
        $cacheKey = 'test';

        //$projectData = false;//$this->redisCache->fetch($cacheKey);
        $projectData = [
            'number'         => $partner->projectNumber,
            'name'           => $partner->projectName,
            'title'          => $partner->projectTitle,
            'description'    => $partner->projectDescription,
            'technicalAraa'  => $partner->technicalArea,
            'programme'      => $partner->programme,
            'programmeCall'  => $partner->programmeCall,
            'primaryCluster' => $partner->primaryCluster,
            'labelDate'      => $partner->labelDate->format(\DateTimeInterface::ATOM),
            'status'         => $partner->status,
        ];

        if (!$projectData) {
            // $projectData['internal_identifier'] = ITEAOFFICE_HOST . '-' . $project->getId();


            $this->redisCache->save($cacheKey, $projectData);
        }

        return $projectData;
    }

}
