<?php

declare(strict_types=1);

namespace Application\Factory;

use Doctrine\Common\Cache\RedisCache;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Redis;

final class RedisFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RedisCache
    {
        $cacheOptions = $container->get('Config')['application_config']['cache_options'];

        $redis    = new RedisCache();
        $instance = new Redis();
        $instance->connect(
            $cacheOptions['adapter']['options']['server']['host'],
            $cacheOptions['adapter']['options']['server']['port'],
            0.3
        );
        if (isset($cacheOptions['adapter']['options']['password'])) {
            $instance->auth($cacheOptions['adapter']['options']['password']);
        }
        $instance->select($cacheOptions['adapter']['options']['database']);
        $redis->setRedis($instance);
        $redis->setNamespace($cacheOptions['adapter']['options']['namespace']);

        return $redis;
    }
}
