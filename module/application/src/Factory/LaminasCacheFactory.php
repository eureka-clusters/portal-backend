<?php

declare(strict_types=1);

namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Cache\Storage;
use Laminas\Cache\Storage\Plugin\Serializer;
use Laminas\ServiceManager\Factory\FactoryInterface;

final class LaminasCacheFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): Storage\Adapter\Redis {
        $cache = new Storage\Adapter\Redis();

        $cache->getOptions()->setTtl(3600);

        $cacheOptions = $container->get('Config')['cache'];

        $cache->getOptions()->setServer(
            [
                'host' => $cacheOptions['adapter']['options']['server']['host'],
                'port' => $cacheOptions['adapter']['options']['server']['host'],
                'timeout' => 0.3
            ]
        );

        $cache->getOptions()->setDatabase($cacheOptions['adapter']['options']['database']);
        $cache->getOptions()->setNamespace($cacheOptions['adapter']['options']['namespace']);

        if (isset($cacheOptions['adapter']['options']['password']) && $cacheOptions['adapter']['options']['password']) {
            $cache->getOptions()->setPassword($cacheOptions['adapter']['options']['password']);
        }

        $plugin = new Storage\Plugin\ExceptionHandler();
        $plugin->getOptions()->setThrowExceptions(false);
        $cache->addPlugin($plugin);

        //Add the serializer
        $plugin = new Serializer();
        $plugin->getOptions()->setThrowExceptions(false);
        $cache->addPlugin($plugin);

        return $cache;
    }
}
