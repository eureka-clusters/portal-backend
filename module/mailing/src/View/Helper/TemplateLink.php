<?php

declare(strict_types=1);

namespace Mailing\View\Helper;

use Application\ValueObject\Link\Link;
use Application\View\Helper\AbstractLink;
use Mailing\Entity\Template;

final class TemplateLink extends AbstractLink
{
    public function __invoke(
        ?Template $template = null,
        string $action = 'view',
        string $show = 'text'
    ): string {
        $linkParams = [];
        $template ??= new Template();

        $routeParams = [];
        $showOptions = [];

        if (! $template->isEmpty()) {
            $routeParams['id']   = $template->getId();
            $showOptions['name'] = $template->getName();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fa-plus',
                    'route' => 'zfcadmin/mailing/template/new',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(message: 'txt-new-mailing-template'),
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fa-link',
                    'route' => 'zfcadmin/mailing/template/view',
                    'text'  => $showOptions[$show] ?? $template->getName(),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/mailing/template/edit',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(message: 'txt-edit-mailing-template'),
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(link: Link::fromArray(params: $linkParams));
    }
}
