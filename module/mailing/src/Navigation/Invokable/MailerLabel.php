<?php

declare(strict_types=1);

namespace Mailing\Navigation\Invokable;

use Application\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;
use Mailing\Entity\Mailer;

use function array_merge;

final class MailerLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate(string: 'txt-nav-mailer');

        if ($this->getEntities()->containsKey(key: Mailer::class)) {
            /** @var Mailer $mailer */
            $mailer = $this->getEntities()->get(key: Mailer::class);

            $page->setParams(
                params: array_merge(
                    $page->getParams(),
                    [
                        'id' => $mailer->getId(),
                    ]
                )
            );
            $label = $mailer->getName();
        }

        if (null === $page->getLabel()) {
            $page->set(property: 'label', value: $label);
        }
    }
}
