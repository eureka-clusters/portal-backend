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
        $label = $this->translate(string: 'txt-nav-view');

        if ($this->getEntities()->containsKey(key: EmailMessage::class)) {
            /** @var EmailMessage $emailMessage */
            $emailMessage = $this->getEntities()->get(key: EmailMessage::class);

            $page->setParams(
                params: array_merge(
                    $page->getParams(),
                    ['id' => $emailMessage->getId()]
                )
            );
            $label = $emailMessage->getSubject();
        }
        $page->set(property: 'label', value: $label);
    }
}
