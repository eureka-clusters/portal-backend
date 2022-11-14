<?php

declare(strict_types=1);

namespace Application\View\Factory;

use Application\Options\ModuleOptions;
use Application\View\Helper\AbstractImage;
use Laminas\Router\RouteStackInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class ImageHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AbstractImage
    {
        $dependencies = [
            $container->get(RouteStackInterface::class),
            $container->get(ModuleOptions::class),
        ];

        return new $requestedName(...$dependencies);
    }
}
