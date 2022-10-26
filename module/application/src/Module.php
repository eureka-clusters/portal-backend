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
        $app = $e->getParam('application');

        /** @var ServiceManager $sm */
        $sm = $app->getServiceManager();

        $injectAclInNavigation = $sm->get(InjectAclInNavigation::class);
        $injectAclInNavigation->attach($app->getEventManager());

        $updateNavigation = $sm->get(UpdateNavigation::class);
        $updateNavigation->attach($app->getEventManager());

        $setTitle = $sm->get(SetTitle::class);
        $setTitle->attach($app->getEventManager());
    }
}
