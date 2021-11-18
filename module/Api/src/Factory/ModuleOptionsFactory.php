<?php

declare(strict_types=1);

namespace Api\Factory;

use Api\Options\ModuleOptions;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

final class ModuleOptionsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ModuleOptions
    {
        $config = $container->get('Config');

        return new ModuleOptions($config['api_options'] ?? []);
    }
}
