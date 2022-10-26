<?php

declare(strict_types=1);

namespace Application\ValueObject\Link;

use JetBrains\PhpStorm\Pure;
use Laminas\Router\RouteStackInterface;

final class LinkRoute
{
    public function __construct(private readonly string $route, private readonly array $routeParams = [], private readonly ?array $queryParams = null, private readonly ?string $fragment = null)
    {
    }

    #[Pure] public static function fromArray(array $params): LinkRoute
    {
        return new self(
            $params['route'] ?? '',
            $params['routeParams'] ?? [],
            $params['queryParams'] ?? null,
            $params['fragment'] ?? null
        );
    }

    public function parse(RouteStackInterface $router, string $serverUrl = ''): string
    {
        return $serverUrl . $router->assemble(
            $this->routeParams,
            ['name' => $this->route, 'query' => $this->queryParams, 'fragment' => $this->fragment]
        );
    }
}
