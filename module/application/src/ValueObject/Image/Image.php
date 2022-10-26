<?php

declare(strict_types=1);

namespace Application\ValueObject\Image;

use Application\Options\ModuleOptions;
use Laminas\Router\RouteStackInterface;

final class Image
{
    public function __construct(private readonly ImageRoute $imageRoute, private readonly ImageDecoration $imageDecoration)
    {
    }

    public static function fromArray(array $params): Image
    {
        return new self(ImageRoute::fromArray($params), ImageDecoration::fromArray($params));
    }

    public function parse(RouteStackInterface $router, ModuleOptions $moduleOptions): string
    {
        return $this->imageDecoration->parse($this->imageRoute->parse($router, $moduleOptions));
    }
}
