<?php

declare(strict_types=1);

namespace Mailing\Navigation\Invokable;

use Application\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;
use Mailing\Entity\EmailMessage;

use function array_merge;

final class EmailMessageLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-view');

        if ($this->getEntities()->containsKey(EmailMessage::class)) {
            /** @var EmailMessage $emailMessage */
            $emailMessage = $this->getEntities()->get(EmailMessage::class);

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    ['id' => $emailMessage->getId()]
                )
            );
            $label = $emailMessage->getSubject();
        }
        $page->set('label', $label);
    }
}
