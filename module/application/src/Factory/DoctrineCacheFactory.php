<?php

declare(strict_types=1);

namespace Application\Factory;

use Doctrine\Common\Cache\CacheProvider;
use DoctrineModule\Cache\LaminasStorageCache;
use Interop\Container\ContainerInterface;
use Laminas\Cache\Storage\Adapter\Redis;
use Laminas\ServiceManager\Factory\FactoryInterface;

final class DoctrineCacheFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): CacheProvider
    {
        $laminasCache = $container->get(Redis::class);

        return new LaminasStorageCache($laminasCache);
    }
}
