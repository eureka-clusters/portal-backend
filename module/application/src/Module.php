<?php

declare(strict_types=1);

namespace Application;

use Application\Event\InjectAclInNavigation;
use Application\Event\SetTitle;
use Application\Event\UpdateNavigation;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ServiceManager\ServiceManager;

final class Module implements ConfigProviderInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(EventInterface $e): void
    {
        $app = $e->getParam(name: 'application');

        /** @var ServiceManager $sm */
        $sm = $app->getServiceManager();

        $injectAclInNavigation = $sm->get(name: InjectAclInNavigation::class);
        $injectAclInNavigation->attach(events: $app->getEventManager());

        $updateNavigation = $sm->get(name: UpdateNavigation::class);
        $updateNavigation->attach(events: $app->getEventManager());

        $setTitle = $sm->get(name: SetTitle::class);
        $setTitle->attach(events: $app->getEventManager());
    }
}
