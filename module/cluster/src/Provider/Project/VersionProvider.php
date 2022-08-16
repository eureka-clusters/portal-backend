<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Version;
use Cluster\Provider\Version\StatusProvider;
use Cluster\Provider\Version\TypeProvider;
use Laminas\Cache\Exception\ExceptionInterface;
use Laminas\Cache\Storage\Adapter\Redis;

class VersionProvider implements ProviderInterface
{
    public function __construct(
        private readonly Redis $cache,
        private readonly TypeProvider $versionTypeProvider,
        private readonly StatusProvider $versionStatusProvider
    ) {
    }

    /**
     * @param Version $version
     * @throws ExceptionInterface
     */
    public function generateArray($version): array
    {
        $cacheKey = $version->getResourceId();

        $versionData = $this->cache->getItem($cacheKey);

        if (!$versionData) {
            $versionData = [
                'id' => $version->getId(),
                'type' => $this->versionTypeProvider->generateArray($version->getType()),
                'status' => $this->versionStatusProvider->generateArray($version->getStatus()),
            ];

            $this->cache->setItem($cacheKey, $versionData);
        }

        return $versionData;
    }
}
