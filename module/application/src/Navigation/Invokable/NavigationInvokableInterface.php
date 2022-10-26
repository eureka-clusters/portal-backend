<?php

declare(strict_types=1);

namespace Application\Navigation\Invokable;

use Laminas\Navigation\Page\Mvc;

/**
 * Interface NavigationInvokableInterface
 */
interface NavigationInvokableInterface
{
    public function __invoke(Mvc $page);
}
