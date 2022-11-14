<?php

declare(strict_types=1);

namespace Admin\View\Helper\OAuth2;

use Api\Entity\OAuth\Client;
use Application\ValueObject\Link\Link;
use Application\ValueObject\Link\LinkDecoration;
use Application\View\Helper\AbstractLink;

final class ClientLink extends AbstractLink
{
    public function __invoke(
        ?Client $client = null,
        string $action = 'view',
        string $show = LinkDecoration::SHOW_TEXT
    ): string {
        $linkParams = [];
        $client   ??= new Client();

        $routeParams = [];
        $showOptions = [];

        if (! $client->isEmpty()) {
            $routeParams['id']       = $client->getId();
            $showOptions['clientId'] = $client->getClientId();
            $showOptions['name']     = $client->getName();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fa-plus',
                    'route' => 'zfcadmin/oauth2/client/new',
                    'text'  => $this->translator->translate(message: 'txt-new-oauth2-client'),
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fa-link',
                    'route' => 'zfcadmin/oauth2/client/view',
                    'text'  => $showOptions[$show] ?? $client->getClientId(),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/oauth2/client/edit',
                    'text'  => $this->translator->translate(message: 'txt-edit-oauth2-client'),
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(link: Link::fromArray(params: $linkParams));
    }
}
