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
        if ($this->getEntities()->containsKey(key: Target::class)) {
            $deeplink = $this->getEntities()->get(key: Target::class);
            $page->setParams(
                params: array_merge(
                    $page->getParams(),
                    [
                        'id' => $deeplink->getId(),
                    ]
                )
            );
            $label = (string) $deeplink;
        } else {
            $label = $this->translator->translate(message: 'txt-nav-view');
        }
        $page->set(property: 'label', value: $label);
    }
}
