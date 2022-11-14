<?php

declare(strict_types=1);

namespace Cluster\Provider\Version;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Version\Type;
use Laminas\Cache\Exception\ExceptionInterface;
use Laminas\Cache\Storage\Adapter\Redis;

class TypeProvider implements ProviderInterface
{
    public function __construct(private readonly Redis $cache)
    {
    }

    /**
     * @param Type $type
     * @throws ExceptionInterface
     */
    public function generateArray($type): array
    {
        $cacheKey = $type->getResourceId();

        $typeData = $this->cache->getItem(key: $cacheKey);

        if (! $typeData) {
            $typeData = [
                'id'          => $type->getId(),
                'type'        => $type->getType(),
                'description' => $type->getDescription(),
            ];

            $this->cache->setItem(key: $cacheKey, value: $typeData);
        }

        return $typeData;
    }
}
