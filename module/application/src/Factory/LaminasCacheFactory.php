<?php

declare(strict_types=1);

namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Cache\Storage\Adapter\Redis;
use Laminas\Cache\Storage\Plugin\ExceptionHandler;
use Laminas\Cache\Storage\Plugin\Serializer;
use Laminas\ServiceManager\Factory\FactoryInterface;

final class LaminasCacheFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): Redis {
        $cache = new Redis();

        $cache->getOptions()->setTtl(ttl: 3600);

        $cacheOptions = $container->get('Config')['cache'];

        $cache->getOptions()->setServer(
            server: [
                'host' => $cacheOptions['adapter']['options']['server']['host'],
                'port' => $cacheOptions['adapter']['options']['server']['host'],
                'timeout' => 0.3
            ]
        );

        $cache->getOptions()->setDatabase(database: $cacheOptions['adapter']['options']['database']);
        $cache->getOptions()->setNamespace(namespace: $cacheOptions['adapter']['options']['namespace']);

        if (isset($cacheOptions['adapter']['options']['password']) && $cacheOptions['adapter']['options']['password']) {
            $cache->getOptions()->setPassword(password: $cacheOptions['adapter']['options']['password']);
        }

        $plugin = new ExceptionHandler();
        $plugin->getOptions()->setThrowExceptions(throwExceptions: false);
        $cache->addPlugin(plugin: $plugin);

        //Add the serializer
        $plugin = new Serializer();
        $plugin->getOptions()->setThrowExceptions(throwExceptions: false);
        $cache->addPlugin(plugin: $plugin);

        return $cache;
    }
}
