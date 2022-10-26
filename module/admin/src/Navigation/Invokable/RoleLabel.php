<?php

declare(strict_types=1);

namespace Admin\Navigation\Invokable;

use Admin\Entity\Role;
use Application\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;

use function array_merge;

final class RoleLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate(string: 'txt-nav-view');

        if ($this->getEntities()->containsKey(key: Role::class)) {
            $entity = $this->getEntities()->get(key: Role::class);
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
        $page->set(property: 'label', value: $label);
    }
}
