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
class OrganisationProvider
{
    private RedisCache $redisCache;

    public function __construct(RedisCache $redisCache)
    {
        $this->redisCache = $redisCache;
    }

    public function generateArray(Entity\Organisation $organisation): array
    {
        $cacheKey = $organisation->getResourceId();

        $organisationData = $this->redisCache->fetch($cacheKey);

        if (true || !$organisationData) {
            $organisationData = [
                'identifier'     => $organisation->getIdentifier(),
                'number'         => $organisation->getNumber(),
                'name'           => $organisation->getName(),
                'title'          => $organisation->getTitle(),
                'description'    => $organisation->getDescription(),
                'technicalArea'  => $organisation->getTechnicalArea(),
                'programme'      => $organisation->getProgramme(),
                'programmeCall'  => $organisation->getProgrammeCall(),
                'primaryCluster' => $organisation->getPrimaryCluster()->getName(),
                'labelDate'      => $organisation->getLabelDate()->format(\DateTimeInterface::ATOM),
                'status'         => $organisation->getStatus()->getStatus(),
            ];


            $this->redisCache->save($cacheKey, $organisationData);
        }

        return $organisationData;
    }
}
