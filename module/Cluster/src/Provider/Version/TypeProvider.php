<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Provider\Version;

use Cluster\Entity;
use Doctrine\Common\Cache\RedisCache;

/**
 *
 */
class TypeProvider
{
    private RedisCache $redisCache;

    public function __construct(RedisCache $redisCache)
    {
        $this->redisCache = $redisCache;
    }

    public function generateArray(Entity\Version\Type $type): array
    {
        $cacheKey = $type->getResourceId();

        $typeData = $this->redisCache->fetch($cacheKey);

        if (!$typeData) {
            $typeData = [
                'id'          => $type->getId(),
                'type'        => $type->getType(),
                'description' => $type->getDescription()
            ];

            $this->redisCache->save($cacheKey, $typeData);
        }

        return $typeData;
    }
}
