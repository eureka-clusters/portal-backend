<?php

declare(strict_types=1);

namespace Cluster\View\Helper;

use Application\ValueObject\Link\Link;
use Application\View\Helper\AbstractLink;
use Cluster\Entity\Project;

final class ProjectLink extends AbstractLink
{
    public function __invoke(
        ?Project $project = null,
        string   $action = 'view',
        string   $show = 'text'
    ): string
    {
        $linkParams = [];
        $project    ??= new Project();

        $routeParams = [];
        $showOptions = [];

        if (!$project->isEmpty()) {
            $routeParams['id']         = $project->getId();
            $showOptions['name']       = $project->getName();
            $showOptions['identifier'] = $project->getIdentifier();
        }

        switch ($action) {
            case 'view':
                $linkParams = [
                    'icon'  => 'fa-link',
                    'route' => 'zfcadmin/project/view',
                    'text'  => $showOptions[$show] ?? $project->getName(),
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(link: Link::fromArray(params: $linkParams));
    }
}
