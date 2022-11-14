<?php

declare(strict_types=1);

namespace Application\ValueObject\Link;

use Laminas\Router\RouteStackInterface;

use function sprintf;

final class Link
{
    public function __construct(private readonly LinkRoute $linkRoute, private readonly LinkDecoration $linkDecoration)
    {
    }

    public static function fromArray(array $params): Link
    {
        return new self(
            linkRoute: LinkRoute::fromArray(params: $params),
            linkDecoration: LinkDecoration::fromArray(
                params: $params
            )
        );
    }

    public function parse(RouteStackInterface $router, string $serverUrl = ''): string
    {
        return sprintf(
            $this->linkDecoration->parse(),
            $this->linkRoute->parse(router: $router, serverUrl: $serverUrl)
        );
    }
}
