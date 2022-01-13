<?php

declare(strict_types=1);

namespace Cluster\Provider\Version;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Version\Status;
use Laminas\Cache\Storage\Adapter\Redis;

class StatusProvider implements ProviderInterface
{
    public function __construct(private Redis $cache)
    {
    }

    /**
     * @param Status $status
     * @return array
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function generateArray($status): array
    {
        $cacheKey = $status->getResourceId();

        $statusData = $this->cache->getItem($cacheKey);

        if (!$statusData) {
            $statusData = [
                'id'     => $status->getId(),
                'status' => $status->getStatus(),
            ];

            $this->cache->setItem($cacheKey, $statusData);
        }

        return $statusData;
    }
}
