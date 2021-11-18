<?php

declare(strict_types=1);

namespace Cluster\Provider\Organisation;

use Cluster\Entity;
use Doctrine\Common\Cache\RedisCache;

class TypeProvider
{
    private RedisCache $redisCache;

    public function __construct(RedisCache $redisCache)
    {
        $this->redisCache = $redisCache;
    }

    public function generateArray(Entity\Organisation\Type $type): array
    {
        $cacheKey = $type->getResourceId();

        $typeData = $this->redisCache->fetch($cacheKey);

        if (! $typeData) {
            $typeData = [
                'id'   => $type->getId(),
                'type' => $type->getType(),
            ];

            $this->redisCache->save($cacheKey, $typeData);
        }

        return $typeData;
    }
}
