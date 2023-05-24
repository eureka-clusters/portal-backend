<?php

declare(strict_types=1);

namespace Cluster\View\Helper\Cluster;

use Application\ValueObject\Link\Link;
use Application\View\Helper\AbstractLink;
use Cluster\Entity\Cluster\Group;

final class GroupLink extends AbstractLink
{
    public function __invoke(
        ?Group $group = null,
        string $action = 'view',
        string $show = 'text'
    ): string
    {
        $linkParams = [];
        $group      ??= new Group();

        $routeParams = [];
        $showOptions = [];

        if (!$group->isEmpty()) {
            $routeParams['id']          = $group->getId();
            $showOptions['name']        = $group->getName();
            $showOptions['description'] = $group->getDescription();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fa-plus',
                    'route' => 'zfcadmin/cluster/group/new',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(message: 'txt-new-cluster-group'),
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fa-link',
                    'route' => 'zfcadmin/cluster/group/view',
                    'text'  => $showOptions[$show] ?? $group->getName(),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/cluster/group/edit',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(message: 'txt-edit-cluster-group'),
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(link: Link::fromArray(params: $linkParams));
    }
}
