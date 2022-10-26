<?php

declare(strict_types=1);

namespace Mailing\Navigation\Invokable;

use Application\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;
use Mailing\Entity\Template;

use function array_merge;

final class TemplateLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate(string: 'txt-nav-email-template');

        if ($this->getEntities()->containsKey(key: Template::class)) {
            /** @var Template $template */
            $template = $this->getEntities()->get(key: Template::class);

            $page->setParams(
                params: array_merge(
                    $page->getParams(),
                    [
                        'id' => $template->getId(),
                    ]
                )
            );
            $label = $template->getName();
        }
        $page->set(property: 'label', value: $label);
    }
}
