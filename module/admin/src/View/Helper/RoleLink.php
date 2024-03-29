<?php

declare(strict_types=1);

namespace Admin\View\Helper;

use Admin\Entity\Role;
use Application\ValueObject\Link\Link;
use Application\View\Helper\AbstractLink;

final class RoleLink extends AbstractLink
{
    public function __invoke(
        ?Role $role = null,
        string $action = 'view',
        string $show = 'text'
    ): string {
        $linkParams = [];
        $role     ??= new Role();

        $routeParams = [];
        $showOptions = [];

        if (! $role->isEmpty()) {
            $routeParams['id']          = $role->getId();
            $showOptions['description'] = $role->getDescription();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fa-plus',
                    'route' => 'zfcadmin/role/new',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(message: 'txt-new-role'),
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fa-link',
                    'route' => 'zfcadmin/role/view',
                    'text'  => $showOptions[$show] ?? $role->getDescription(),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/role/edit',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(message: 'txt-edit-role'),
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(link: Link::fromArray(params: $linkParams));
    }
}
