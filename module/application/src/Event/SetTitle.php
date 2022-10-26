<?php

declare(strict_types=1);

namespace Application\Event;

use Application\Version\Version as ApplicationVersion;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Helper\Placeholder\Container\AbstractContainer;
use Laminas\View\Renderer\PhpRenderer;

class SetTitle extends AbstractListenerAggregate
{
    public function __construct(private readonly PhpRenderer $renderer)
    {
    }

    public function setHeadLink(): void
    {
        $this->renderer->headLink()->appendStylesheet(
            '//cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css',
            'all',
            null,
            null
        );

        $this->renderer->headLink(
            ['rel' => 'icon', 'type' => 'image/vnd.microsoft.icon', 'href' => 'favicon.ico'],
            AbstractContainer::PREPEND
        );
    }

    public function setHeadMeta(): void
    {
        $this->renderer->headMeta()->appendHttpEquiv('X-UA-Compatible', 'IE=edge,chrome=1');
        $this->renderer->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1.0');
        $this->renderer->headMeta()->appendName('author', 'Dr. Ir. Johan van der Heide <info@jield.nl>');
        $this->renderer->headMeta()->appendName('renderer', 'Laminas');
        $this->renderer->headMeta()->appendName('renderer', 'ITEA');
    }

    public function setHeadScript(): void
    {
        $this->renderer->headScript()->appendFile('//cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js');
    }

    public function setHeadTitle(): void
    {
        /**
         * @phpstan-ignore-next-line
         */
        $this->renderer->headTitle()->setSeparator(' - ');
        /**
         * @phpstan-ignore-next-line
         */
        $this->renderer->headTitle()->append('Eureka Clusters Backend Application');
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, $this->setHeadLink(...), priority: 1000);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, $this->setHeadMeta(...), priority: 1000);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, $this->setHeadScript(...), priority: 1000);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, $this->setHeadTitle(...), priority: 1000);
    }
}
