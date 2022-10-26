<?php

declare(strict_types=1);

namespace Deeplink\View\Helper\Deeplink;

use Application\ValueObject\Link\Link;
use Application\ValueObject\Link\LinkDecoration;
use Application\View\Helper\AbstractLink;
use Deeplink\Entity\Target;

final class TargetLink extends AbstractLink
{
    public function __invoke(
        ?Target $target = null,
        string $action = 'view',
        string $show = LinkDecoration::SHOW_TEXT
    ): string {
        $linkParams = [];
        $target   ??= new Target();

        $routeParams = [];
        $showOptions = [];
        if (! $target->isEmpty()) {
            $routeParams['id']     = $target->getId();
            $showOptions['target'] = $target->getTarget();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fa-plus',
                    'route' => 'zfcadmin/deeplink/target/new',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-new-target'),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/deeplink/target/edit',
                    'text'  => $showOptions[$show]
                        ?? $this->translator->translate('txt-edit-target'),
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fa-link',
                    'route' => 'zfcadmin/deeplink/target/view',
                    'text'  => $showOptions[$show] ?? $target->getTarget(),
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
