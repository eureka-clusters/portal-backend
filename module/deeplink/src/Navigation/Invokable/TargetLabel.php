<?php

declare(strict_types=1);

namespace Deeplink\Navigation\Invokable;

use Application\Navigation\Invokable\AbstractNavigationInvokable;
use Deeplink\Entity\Target;
use Laminas\Navigation\Page\Mvc;

use function array_merge;

class TargetLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        if ($this->getEntities()->containsKey(Target::class)) {
            $deeplink = $this->getEntities()->get(Target::class);
            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $deeplink->getId(),
                    ]
                )
            );
            $label = (string) $deeplink;
        } else {
            $label = $this->translator->translate('txt-nav-view');
        }
        $page->set('label', $label);
    }
}
