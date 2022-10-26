<?php

declare(strict_types=1);

namespace Mailing\View\Helper;

use Application\ValueObject\Link\Link;
use Application\View\Helper\AbstractLink;
use Mailing\Entity\Sender;

final class SenderLink extends AbstractLink
{
    public function __invoke(
        ?Sender $sender = null,
        string $action = 'view',
        string $show = 'text'
    ): string {
        $linkParams = [];
        $sender ??= new Sender();

        $routeParams = [];
        $showOptions = [];

        if (! $sender->isEmpty()) {
            $routeParams['id']   = $sender->getId();
            $showOptions['name'] = $sender->getSender();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fa-plus',
                    'route' => 'zfcadmin/mailing/sender/new',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(message: 'txt-new-sender'),
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fa-link',
                    'route' => 'zfcadmin/mailing/sender/view',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(message: 'txt-view-sender'),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/mailing/sender/edit',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(message: 'txt-edit-sender'),
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(link: Link::fromArray(params: $linkParams));
    }
}
