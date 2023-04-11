<?php

declare(strict_types=1);

namespace Reporting\Navigation\Invokable;

use Application\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;
use Reporting\Entity\StorageLocation;

use function array_merge;

final class StorageLocationLabel extends AbstractNavigationInvokable
{
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate(string: 'txt-nav-mailer');

        if ($this->getEntities()->containsKey(key: StorageLocation::class)) {
            /** @var StorageLocation $storageLocation */
            $storageLocation = $this->getEntities()->get(key: StorageLocation::class);

            $page->setParams(
                params: array_merge(
                    $page->getParams(),
                    [
                        'id' => $storageLocation->getId(),
                    ]
                )
            );
            $label = $storageLocation->getName();
        }

        if (null === $page->getLabel()) {
            $page->set(property: 'label', value: $label);
        }
    }
}
