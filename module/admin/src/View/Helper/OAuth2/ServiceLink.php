<?php

declare(strict_types=1);

namespace Admin\View\Helper\OAuth2;

use Api\Entity\OAuth\Service;
use Application\ValueObject\Link\Link;
use Application\ValueObject\Link\LinkDecoration;
use Application\View\Helper\AbstractLink;

final class ServiceLink extends AbstractLink
{
    public function __invoke(
        ?Service $service = null,
        string $action = 'view',
        string $show = LinkDecoration::SHOW_TEXT
    ): string {
        $linkParams = [];
        $service ??= new Service();

        $routeParams = [];

        if (!$service->isEmpty()) {
            $routeParams['id'] = $service->getId();
            $routeParams['name'] = $service->getName();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fa-plus',
                    'route' => 'zfcadmin/oauth2/service/new',
                    'text' => $this->translator->translate(message: 'txt-new-oauth2-service'),
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'fa-link',
                    'route' => 'zfcadmin/oauth2/service/view',
                    'text' => $service->getName(),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/oauth2/service/edit',
                    'text' => $this->translator->translate(message: 'txt-edit-oauth2-service'),
                ];
                break;
            case 'login':
                $linkParams = [
                    'icon' => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/oauth2/service/login',
                    'text' => $this->translator->translate(message: 'txt-edit-oauth2-service'),
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(link: Link::fromArray(params: $linkParams));
    }
}
