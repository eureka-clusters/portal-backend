<?php

declare(strict_types=1);

namespace Cluster\Provider\Version;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Version\Type;
use Laminas\Cache\Storage\Adapter\Redis;

class TypeProvider implements ProviderInterface
{
    public function __construct(private Redis $cache)
    {
    }

    /**
     * @param Type $type
     * @return array
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function generateArray($type): array
    {
        $cacheKey = $type->getResourceId();

        $typeData = $this->cache->getItem($cacheKey);

        if (!$typeData) {
            $typeData = [
                'id'          => $type->getId(),
                'type'        => $type->getType(),
                'description' => $type->getDescription(),
            ];

            $this->cache->setItem($cacheKey, $typeData);
        }

        return $typeData;
    }
}
