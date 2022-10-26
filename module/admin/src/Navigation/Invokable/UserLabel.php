<?php

declare(strict_types=1);

namespace Admin\Navigation\Invokable;

use Admin\Entity\User;
use Application\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;

use function array_merge;

final class UserLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-user');

        if ($this->getEntities()->containsKey(User::class)) {
            $entity = $this->getEntities()->get(User::class);
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
