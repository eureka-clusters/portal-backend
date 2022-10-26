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
        $label = $this->translate('txt-nav-email-template');

        if ($this->getEntities()->containsKey(Template::class)) {
            /** @var Template $template */
            $template = $this->getEntities()->get(Template::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $template->getId(),
                    ]
                )
            );
            $label = $template->getName();
        }
        $page->set('label', $label);
    }
}
