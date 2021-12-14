<?php

declare(strict_types=1);

namespace Cluster\Provider\Version;

use Cluster\Entity\Version\Type;
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
                'id'          => $type->getId(),
                'type'        => $type->getType(),
                'description' => $type->getDescription(),
            ];

            $this->redisCache->save($cacheKey, $typeData);
        }

        return $typeData;
    }
}
