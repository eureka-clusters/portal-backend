<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Cluster\Entity\Project\Status;
use Cluster\Entity;
use Doctrine\Common\Cache\RedisCache;

class StatusProvider
{
    public function __construct(private RedisCache $redisCache)
    {
    }

    public function generateArray(Status $status): array
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
