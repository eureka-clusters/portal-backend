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
        $label = $this->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(Role::class)) {
            $entity = $this->getEntities()->get(Role::class);
            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $entity->getId(),
                    ]
                )
            );
            $label = (string) $entity;
        }
        $page->set('label', $label);
    }
}
