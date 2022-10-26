<?php

declare(strict_types=1);

namespace Application\ValueObject\Image;

use Application\Options\ModuleOptions;
use JetBrains\PhpStorm\Pure;
use Laminas\Router\RouteStackInterface;

final class ImageRoute
{
    public function __construct(private readonly string $route, private readonly array $routeParams)
    {
    }

    #[Pure] public static function fromArray(array $params): ImageRoute
    {
        return new self(
            $params['route'] ?? '',
            $params['routeParams'] ?? []
        );
    }

    public function parse(RouteStackInterface $router, ModuleOptions $moduleOptions): string
    {
        return $moduleOptions->getServerUrl() . $router->assemble(
            $this->routeParams,
            ['name' => $this->route]
        );
    }
}
