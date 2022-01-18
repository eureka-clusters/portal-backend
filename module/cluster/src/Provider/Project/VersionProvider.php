<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Version;
use Cluster\Provider\Version\StatusProvider;
use Cluster\Provider\Version\TypeProvider;
use Laminas\Cache\Storage\Adapter\Redis;

class VersionProvider implements ProviderInterface
{
    public function __construct(
        private Redis $cache,
        private TypeProvider $versionTypeProvider,
        private StatusProvider $versionStatusProvider
    ) {
    }

    /**
     * @param Version $version
     * @return array
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function generateArray($version): array
    {
        $cacheKey = $version->getResourceId();

        $versionData = $this->cache->getItem($cacheKey);

        if (!$versionData) {
            $versionData = [
                'id'     => $version->getId(),
                'type'   => $this->versionTypeProvider->generateArray($version->getType()),
                'status' => $this->versionStatusProvider->generateArray($version->getStatus()),
            ];

            $this->cache->setItem($cacheKey, $versionData);
        }

        return $versionData;
    }
}
