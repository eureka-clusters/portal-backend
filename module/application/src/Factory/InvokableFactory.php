<?php

declare(strict_types=1);

namespace Application\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class InvokableFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return null === $options ? new $requestedName($container) : new $requestedName($container, $options);
    }
}
