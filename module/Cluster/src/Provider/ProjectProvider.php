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
    private const STATUS_CONCEPT   = 'Concept';
    private const STATUS_LABELLED  = 'Labelled';
    private const STATUS_RUNNING   = 'Running';
    private const STATUS_COMPLETED = 'Completed';
    private const STATUS_STOPPED   = 'Stopped';

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
            'number'          => $partner->projectNumber,
            'name'            => $partner->projectName,
            'title'           => $partner->projectTitle,
            'description'     => $partner->projectDescription,
            'technical_araa'  => $partner->technicalArea,
            'programme'       => $partner->programme,
            'programme_call'  => $partner->programmeCall,
            'primary_cluster' => $partner->primaryCluster,
            'label_date'      => $partner->labelDate->format(\DateTimeInterface::ATOM),
            'status'          => $partner->status,
        ];

        if (!$projectData) {
            // $projectData['internal_identifier'] = ITEAOFFICE_HOST . '-' . $project->getId();


            $this->redisCache->save($cacheKey, $projectData);
        }

        return $projectData;
    }
}
