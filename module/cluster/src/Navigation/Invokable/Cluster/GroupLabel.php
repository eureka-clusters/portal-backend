<?php

declare(strict_types=1);

namespace Cluster\Navigation\Invokable\Cluster;

use Application\Navigation\Invokable\AbstractNavigationInvokable;
use Cluster\Entity\Cluster\Group;
use Laminas\Navigation\Page\Mvc;
use function array_merge;

final class GroupLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate(string: 'txt-nav-view');

        if ($this->getEntities()->containsKey(key: Group::class)) {
            $entity = $this->getEntities()->get(key: Group::class);
            $page->setParams(
                params: array_merge(
                    $page->getParams(),
                    [
                        'id' => $entity->getId(),
                    ]
                )
            );
            $label = (string)$entity;
        }

        if (null === $page->getLabel()) {
            $page->set(property: 'label', value: $label);
        }
    }
}
