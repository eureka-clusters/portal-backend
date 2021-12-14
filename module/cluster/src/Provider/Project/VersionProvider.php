<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Cluster\Entity\Project\Version;
use Cluster\Entity;
use Cluster\Provider\Version\StatusProvider;
use Cluster\Provider\Version\TypeProvider;
use Doctrine\Common\Cache\RedisCache;

class VersionProvider
{
    public function __construct(private RedisCache $redisCache, private TypeProvider $versionTypeProvider, private StatusProvider $versionStatusProvider)
    {
    }

    public function generateArray(Version $version): array
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
