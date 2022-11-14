<?php

declare(strict_types=1);

namespace Application\Factory;

use Doctrine\Common\Cache\CacheProvider;
use DoctrineModule\Cache\LaminasStorageCache;
use interop\container\containerinterface;
use Laminas\Cache\Storage\Adapter\Redis;
use Laminas\ServiceManager\Factory\FactoryInterface;

final class DoctrineCacheFactory implements FactoryInterface
{
    public function __invoke(containerinterface $container, $requestedName, ?array $options = null): CacheProvider
    {
        $laminasCache = $container->get(Redis::class);

        return new LaminasStorageCache(storage: $laminasCache);
    }
}
