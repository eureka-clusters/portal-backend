<?php

declare(strict_types=1);

namespace Application\Factory;

use Application\Service\FormService;
use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class FormServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): FormService
    {
        return new $requestedName($container, $container->get(EntityManager::class));
    }
}
