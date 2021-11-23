<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Cluster\Entity;
use Cluster\Provider\Version\StatusProvider;
use Cluster\Provider\Version\TypeProvider;
use Doctrine\Common\Cache\RedisCache;

class VersionProvider
{
    private RedisCache $redisCache;
    private TypeProvider $versionTypeProvider;
    private StatusProvider $versionStatusProvider;

    public function __construct(
        RedisCache $redisCache,
        TypeProvider $versionTypeProvider,
        StatusProvider $versionStatusProvider
    ) {
        $this->redisCache            = $redisCache;
        $this->versionTypeProvider   = $versionTypeProvider;
        $this->versionStatusProvider = $versionStatusProvider;
    }

    public function generateArray(Entity\Project\Version $version): array
    {
        $cacheKey = $version->getResourceId();

        $versionData = $this->redisCache->fetch($cacheKey);

        if (! $versionData) {
            $versionData = [
                'id'     => $version->getId(),
                'type'   => $this->versionTypeProvider->generateArray($version->getType()),
                'status' => $this->versionStatusProvider->generateArray($version->getStatus()),
            ];

            $this->redisCache->save($cacheKey, $versionData);
        }

        return $versionData;
    }
}
