<?php

declare(strict_types=1);

namespace Application\Navigation\Invokable;

use Application\Event\UpdateNavigation;
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Navigation\Page\Mvc;
use Psr\Container\ContainerInterface;

abstract class AbstractNavigationInvokable implements NavigationInvokableInterface
{
    protected mixed $translator;

    public function __construct(protected ContainerInterface $container)
    {
        $this->translator = $container->get(TranslatorInterface::class);
    }

    abstract public function __invoke(Mvc $page): void;

    protected function getEntities(): ArrayCollection
    {
        /** @var UpdateNavigation $updateNavigation */
        $updateNavigation = $this->container->get(UpdateNavigation::class);

        return $updateNavigation->getEntities();
    }

    protected function translate(string $string): string
    {
        return $this->translator->translate($string);
    }
}
