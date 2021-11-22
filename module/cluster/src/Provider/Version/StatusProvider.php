<?php

declare(strict_types=1);

namespace Cluster\Provider\Version;

use Cluster\Entity;
use Doctrine\Common\Cache\RedisCache;

class StatusProvider
{
    private RedisCache $redisCache;

    public function __construct(RedisCache $redisCache)
    {
        $this->redisCache = $redisCache;
    }

    public function generateArray(Entity\Version\Status $status): array
    {
        $cacheKey = $status->getResourceId();

        $statusData = $this->redisCache->fetch($cacheKey);

        if (! $statusData) {
            $statusData = [
                'id'     => $status->getId(),
                'status' => $status->getStatus(),
            ];

            $this->redisCache->save($cacheKey, $statusData);
        }

        return $statusData;
    }
}
