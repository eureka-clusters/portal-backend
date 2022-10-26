<?php

declare(strict_types=1);

namespace Admin\View\Helper\OAuth2;

use Api\Entity\OAuth\Scope;
use Application\ValueObject\Link\Link;
use Application\ValueObject\Link\LinkDecoration;
use Application\View\Helper\AbstractLink;

final class ScopeLink extends AbstractLink
{
    public function __invoke(
        ?Scope $scope = null,
        string $action = 'view',
        string $show = LinkDecoration::SHOW_TEXT
    ): string {
        $linkParams = [];
        $scope ??= new Scope();

        $routeParams = [];

        if (!$scope->isEmpty()) {
            $routeParams['id'] = $scope->getId();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fa-plus',
                    'route' => 'zfcadmin/oauth2/scope/new',
                    'text' => $this->translator->translate(message: 'txt-new-oauth2-scope'),
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'fa-link',
                    'route' => 'zfcadmin/oauth2/scope/view',
                    'text' => $scope->getScope(),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/oauth2/scope/edit',
                    'text' => $this->translator->translate(message: 'txt-edit-oauth2-scope'),
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(link: Link::fromArray(params: $linkParams));
    }
}
