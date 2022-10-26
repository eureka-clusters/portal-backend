<?php

declare(strict_types=1);

namespace Deeplink\View\Helper;

use Application\ValueObject\Link\Link;
use Application\ValueObject\Link\LinkDecoration;
use Application\View\Helper\AbstractLink;
use Deeplink\Entity\Deeplink;

final class DeeplinkLink extends AbstractLink
{
    public function __invoke(
        Deeplink $deepLink,
        string $action = 'view',
        string $show = LinkDecoration::SHOW_TEXT
    ): string {
        $routeParams = [];
        $showOptions = [];

        $routeParams['hash'] = $deepLink->getHash();

        $showOptions['full']   = $deepLink->getHash();
        $showOptions['target'] = $deepLink->getTarget()->getTarget();

        $linkParams = [
            'route' => 'deeplink',
            'text'  => $showOptions[$show] ?? $deepLink->getTarget()->getTarget(),
        ];

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
