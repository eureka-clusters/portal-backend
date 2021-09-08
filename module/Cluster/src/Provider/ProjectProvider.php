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

    public function generateArray(Entity\Statistics\Partner $partner): array
    {
        $cacheKey = $partner->identifier;

        $projectData = $this->redisCache->fetch($cacheKey);

        if (!$projectData) {
            $projectData = [
                'identifier'     => $partner->identifier,
                'number'         => $partner->projectNumber,
                'name'           => $partner->projectName,
                'title'          => $partner->projectTitle,
                'description'    => $partner->projectDescription,
                'technicalArea'  => $partner->technicalArea,
                'programme'      => $partner->programme,
                'programmeCall'  => $partner->programmeCall,
                'primaryCluster' => $partner->primaryCluster,
                'labelDate'      => $partner->labelDate->format(\DateTimeInterface::ATOM),
                'status'         => $partner->status,
            ];


            $this->redisCache->save($cacheKey, $projectData);
        }

        return $projectData;
    }
}
