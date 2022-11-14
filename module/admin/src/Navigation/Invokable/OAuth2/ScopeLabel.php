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
        $label = $this->translator->translate(message: 'txt-nav-view');

        if ($this->getEntities()->containsKey(key: Scope::class)) {
            $entity = $this->getEntities()->get(key: Scope::class);
            $page->setParams(
                params: array_merge(
                    $page->getParams(),
                    [
                        'id' => $entity->getId(),
                    ]
                )
            );
            $label = (string) $entity;
        }

        if (null === $page->getLabel()) {
            $page->set(property: 'label', value: $label);
        }
    }
}
