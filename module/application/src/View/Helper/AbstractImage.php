<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Application\Options\ModuleOptions;
use Application\ValueObject\Image\Image;
use Laminas\Router\RouteStackInterface;

abstract class AbstractImage
{
    public function __construct(private readonly RouteStackInterface $router, private readonly ModuleOptions $moduleOptions)
    {
    }

    protected function parse(?Image $image): string
    {
        return $image === null ? '' : $image->parse($this->router, $this->moduleOptions);
    }
}
