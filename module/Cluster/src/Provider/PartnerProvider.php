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
class PartnerProvider
{
    private RedisCache $redisCache;

    public function __construct(RedisCache $redisCache)
    {
        $this->redisCache = $redisCache;
    }

    public function generateArray(Entity\Statistics\Partner $partner): array
    {
        $cacheKey = $partner->partnerIdentifier;

        $partnerData = $this->redisCache->fetch($cacheKey);

        if (!$partnerData) {
            $partnerData = [
                'identifier' => $partner->partnerIdentifier,
                'name'       => $partner->partner,
                'type'       => $partner->partnerType,
                'country'    => $partner->country
            ];

            $this->redisCache->save($cacheKey, $partnerData);
        }

        return $partnerData;
    }
}
