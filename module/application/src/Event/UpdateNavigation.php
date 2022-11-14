<?php

declare(strict_types=1);

namespace Application\Event;

use Application\Navigation\Invokable\NavigationInvokableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Navigation\Navigation;
use Laminas\Navigation\Page\Mvc;
use Laminas\Router\RouteMatch;
use Psr\Container\ContainerInterface;

class UpdateNavigation extends AbstractListenerAggregate
{
    protected ArrayCollection $entities;

    private readonly TranslatorInterface $translator;

    private readonly Navigation $navigation;

    private ?RouteMatch $routeMatch = null;

    private readonly EntityManager $entityManager;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->translator = $container->get(TranslatorInterface::class);
        $this->navigation = $container->get(Navigation::class);
        $this->entityManager = $this->container->get(EntityManager::class);

        $this->entities = new ArrayCollection();
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, $this->onRoute(...), priority: -1000);
    }

    public function onRoute(MvcEvent $event): void
    {
        $this->routeMatch = $event->getRouteMatch();

        /** @var Mvc $page */
        $page = $this->navigation->findOneBy(property: 'route', value: $this->routeMatch->getMatchedRouteName());

        if ($page instanceof Mvc) {
            // Set active
            $page->setActive(active: true);

            // Merge all route params with navigation params
            $routeParams = $this->routeMatch->getParams();
            $page->setParams(params: array_merge($page->getParams(), $routeParams));

            // Custom navigation params from module.config.navigation.php
            $pageCustomParams = $page->get(property: 'params');

            if (isset($pageCustomParams['entities']) && is_array(value: $pageCustomParams['entities'])) {
                foreach ($pageCustomParams['entities'] as $routeParam => $entityClass) {
                    // The routeParam can be aliased
                    $routeParamKey = $routeParam;
                    if (isset($pageCustomParams['routeParam']) && array_key_exists(
                        key: $routeParam,
                        array: $pageCustomParams['routeParam']
                    )) {
                        $routeParamKey = $pageCustomParams['routeParam'][$routeParam];
                    }

                    //When we have a property we want to match on, we overrule the routeParam witht his
                    if (isset($pageCustomParams['property'])) {
                        $routeParam = $pageCustomParams['property'];
                    }

                    if (null !== $entityClass && class_exists(class: $entityClass)) {
                        $repository = $this->entityManager->getRepository(entityName: $entityClass);

                        $entity = $repository->findOneBy(
                            criteria: [$routeParam => $this->routeMatch->getParam(name: $routeParamKey)]
                        );

                        if (null === $entity) {
                            if (
                                defined(constant_name: 'NAVELA_ENVIRONMENT')
                                && (NAVELA_ENVIRONMENT === 'development')
                            ) {
                                print sprintf(
                                    "Can not load '%s' by '%s' via '%s' value(%s)",
                                    $entityClass,
                                    $routeParam,
                                    $routeParamKey,
                                    $this->routeMatch->getParam(name: $routeParamKey)
                                );
                            }
                        } else {
                            $this->entities->set(key: $entityClass, value: $entity);
                        }
                    }
                }
            }

            $this->updateNavigation(page: $page);
        }
    }

    public function getEntities(): ArrayCollection
    {
        return $this->entities;
    }

    protected function updateNavigation(Mvc $page): void
    {
        $page->setVisible(visible: true);
        $pageCustomParams = $page->get(property: 'params');

        // Provide the setter callables with the entity and route params
        if (isset($pageCustomParams['invokables']) && is_array(value: $pageCustomParams['invokables'])) {
            foreach ($pageCustomParams['invokables'] as $invokable) {
                // Get the invokable from the service locator
                if ($this->container->has($invokable)) {
                    $instance = $this->container->get($invokable);
                    if ($instance instanceof NavigationInvokableInterface) {
                        $instance(page: $page);
                    } else {
                        throw new InvalidArgumentException(message: 'Can\'t invoke callable ' . $invokable);
                    }
                // Not found
                } else {
                    throw new InvalidArgumentException(message: 'Servicelocator can\'t find invokable ' . $invokable);
                }
            }
        }

        // Traverse up the navigation with the current entities
        $parentPage = $page->getParent();
        if ($parentPage instanceof Mvc) {
            $routeParams = $this->routeMatch->getParams();
            $parentPage->setParams(params: array_merge($parentPage->getParams(), $routeParams));
            $this->updateNavigation(page: $parentPage);
        }
    }

    protected function translate(string $string): string
    {
        return $this->translator->translate(message: $string);
    }
}
