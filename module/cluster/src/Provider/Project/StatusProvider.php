<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Status;
use Laminas\Cache\Exception\ExceptionInterface;
use Laminas\Cache\Storage\Adapter\Redis;

class StatusProvider implements ProviderInterface
{
    public function __construct(private readonly Redis $cache)
    {
    }

    /**
     * @param Status $status
     * @throws ExceptionInterface
     */
    public function generateArray($status): array
    {
        $cacheKey = $status->getResourceId();

        $statusData = $this->cache->getItem($cacheKey);

        if (!$statusData) {
            $statusData = [
                'id' => $status->getId(),
                'status' => $status->getStatus(),
            ];

            $this->cache->setItem($cacheKey, $statusData);
        }

        return $statusData;
    }
}
