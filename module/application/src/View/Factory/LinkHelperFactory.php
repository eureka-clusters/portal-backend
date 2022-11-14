<?php

declare(strict_types=1);

namespace Application\View\Factory;

use Application\Options\ModuleOptions;
use Application\View\Helper\AbstractLink;
use BjyAuthorize\Service\Authorize;
use Jield\Authorize\Service\AssertionService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Router\RouteStackInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class LinkHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AbstractLink
    {
        $dependencies = [
            $container->get(AssertionService::class),
            $container->get(Authorize::class),
            $container->get(RouteStackInterface::class),
            $container->get(TranslatorInterface::class),
            $container->get(ModuleOptions::class),
        ];

        return new $requestedName(...$dependencies);
    }
}
