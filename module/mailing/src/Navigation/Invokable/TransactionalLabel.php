<?php

declare(strict_types=1);

namespace Mailing\Navigation\Invokable;

use Application\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;
use Mailing\Entity\Transactional;

use function array_merge;

final class TransactionalLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate(string: 'txt-nav-transactional-email');

        if ($this->getEntities()->containsKey(key: Transactional::class)) {
            /** @var Transactional $transactional */
            $transactional = $this->getEntities()->get(key: Transactional::class);

            $page->setParams(
                params: array_merge(
                    $page->getParams(),
                    [
                        'id' => $transactional->getId(),
                    ]
                )
            );
            $label = $transactional->getName();
        }
        $page->set(property: 'label', value: $label);
    }
}
