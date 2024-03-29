<?php

declare(strict_types=1);

namespace Mailing\Navigation\Invokable;

use Application\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;
use Mailing\Entity\Sender;

use function array_merge;

final class SenderLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate(string: 'txt-nav-sender');

        if ($this->getEntities()->containsKey(key: Sender::class)) {
            /** @var Sender $sender */
            $sender = $this->getEntities()->get(key: Sender::class);

            $page->setParams(
                params: array_merge(
                    $page->getParams(),
                    [
                        'id' => $sender->getId(),
                    ]
                )
            );
            $label = $sender->getSender();
        }
        $page->set(property: 'label', value: $label);
    }
}
