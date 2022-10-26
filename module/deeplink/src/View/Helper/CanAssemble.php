<?php

declare(strict_types=1);

namespace Deeplink\View\Helper;

use Laminas\Router\Http\TreeRouteStack;
use Laminas\View\Helper\AbstractHelper;
use RuntimeException;

final class CanAssemble extends AbstractHelper
{
    public function __construct(private readonly TreeRouteStack $router)
    {
    }

    public function __invoke(string $route): bool
    {
        try {
            $this->router->assemble([], ['name' => $route]);

            return true;
        } catch (RuntimeException) {
            return false;
        }
    }
}
