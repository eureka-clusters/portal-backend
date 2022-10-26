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
        $label = $this->translate('txt-nav-mailer');

        if ($this->getEntities()->containsKey(Mailer::class)) {
            /** @var Mailer $mailer */
            $mailer = $this->getEntities()->get(Mailer::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $mailer->getId(),
                    ]
                )
            );
            $label = $mailer->getName();
        }

        if (null === $page->getLabel()) {
            $page->set('label', $label);
        }
    }
}
