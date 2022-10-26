<?php

declare(strict_types=1);

namespace Application\Factory;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Laminas\InputFilter\InputFilter;
use Laminas\ServiceManager\Factory\FactoryInterface;

final class InputFilterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): InputFilter
    {
        return new $requestedName($container->get(EntityManager::class));
    }
}
