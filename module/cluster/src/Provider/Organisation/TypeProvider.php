<?php

declare(strict_types=1);

namespace Cluster\Provider\Organisation;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Organisation\Type;
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

        if (!$typeData) {
            $typeData = [
                'id' => $type->getId(),
                'type' => $type->getType(),
            ];

            $this->cache->setItem(key: $cacheKey, value: $typeData);
        }

        return $typeData;
    }
}
