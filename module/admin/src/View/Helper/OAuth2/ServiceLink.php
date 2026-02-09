<?php

declare(strict_types=1);

namespace Admin\View\Helper\OAuth2;

use Api\Entity\OAuth\Service;
use Api\Enum\OAuth2\ServiceTypeEnum;
use Application\ValueObject\Link\Link;
use Application\ValueObject\Link\LinkDecoration;
use Application\View\Helper\AbstractLink;

final class ServiceLink extends AbstractLink
{
    public function __invoke(
        ?Service         $service = null,
        string           $action = 'view',
        string           $show = LinkDecoration::SHOW_TEXT,
        ?ServiceTypeEnum $serviceType = null
    ): string
    {
        $linkParams = [];
        $service    ??= new Service();

        $routeParams = [];

        if (!$service->isEmpty()) {
            $routeParams['id']   = $service->getId();
            $routeParams['name'] = $service->getName();
        }

        if ($serviceType instanceof \Api\Enum\OAuth2\ServiceTypeEnum) {
            $routeParams['serviceType'] = $serviceType->value;
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fa-plus',
                    'route' => 'zfcadmin/oauth2/service/new',
                    'text'  => sprintf($this->translator->translate(message: 'txt-new-oauth2-service-of-type-%s'), $serviceType->toString()),
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fa-link',
                    'route' => 'zfcadmin/oauth2/service/view',
                    'text'  => $service->getName(),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/oauth2/service/edit',
                    'text'  => $this->translator->translate(message: 'txt-edit-oauth2-service'),
                ];
                break;
            case 'update-client-secret':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/oauth2/service/update-client-secret',
                    'text'  => $this->translator->translate(message: 'txt-update-client-secret'),
                ];
                break;
            case 'update-private-key':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/oauth2/service/update-private-key',
                    'text'  => $this->translator->translate(message: 'txt-update-private-key'),
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(link: Link::fromArray(params: $linkParams));
    }
}
