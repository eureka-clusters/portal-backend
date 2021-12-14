<?php

declare(strict_types=1);

namespace Cluster\Provider\Organisation;

use Cluster\Entity\Organisation\Type;
use Cluster\Entity;
use Doctrine\Common\Cache\RedisCache;

class TypeProvider
{
    public function __construct(private RedisCache $redisCache)
    {
    }

    public function generateArray(Type $type): array
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
