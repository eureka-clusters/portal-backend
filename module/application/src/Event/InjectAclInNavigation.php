<?php

declare(strict_types=1);

namespace Application\Event;

use Jield\Authorize\Service\AuthorizeService;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Helper\Navigation;

class InjectAclInNavigation extends AbstractListenerAggregate
{
    public function __construct(private readonly AuthorizeService $authorizeService)
    {
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, $this->onRender(...), -1000);
    }

    public function onRender(MvcEvent $event): void
    {
        Navigation::setDefaultAcl($this->authorizeService->getAcl());
        Navigation::setDefaultRole($this->authorizeService->getIdentityAsRole());
    }
}
