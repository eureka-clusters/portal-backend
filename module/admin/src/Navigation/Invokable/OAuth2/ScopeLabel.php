<?php

declare(strict_types=1);

namespace Admin\Navigation\Invokable\OAuth2;

use Api\Entity\OAuth\Scope;
use Application\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;

use function array_merge;

final class ScopeLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translator->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Scope::class)) {
            $entity = $this->getEntities()->get(Scope::class);
            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $entity->getId(),
                    ]
                )
            );
            $label = (string)$entity;
        }

        if (null === $page->getLabel()) {
            $page->set('label', $label);
        }
    }
}
