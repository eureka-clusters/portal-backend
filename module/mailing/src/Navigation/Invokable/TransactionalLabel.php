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
        $label = $this->translate('txt-nav-transactional-email');

        if ($this->getEntities()->containsKey(Transactional::class)) {
            /** @var Transactional $transactional */
            $transactional = $this->getEntities()->get(Transactional::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $transactional->getId(),
                    ]
                )
            );
            $label = $transactional->getName();
        }
        $page->set('label', $label);
    }
}
