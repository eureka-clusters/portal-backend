<?php

declare(strict_types=1);

namespace Mailing\View\Helper;

use Application\ValueObject\Link\Link;
use Application\ValueObject\Link\LinkDecoration;
use Application\View\Helper\AbstractLink;
use Mailing\Entity\Transactional;

final class TransactionalLink extends AbstractLink
{
    public function __invoke(
        ?Transactional $transactional = null,
        string $action = 'view',
        string $show = LinkDecoration::SHOW_TEXT
    ): string {
        $linkParams      = [];
        $transactional ??= new Transactional();

        $routeParams = [];
        $showOptions = [];

        if (! $transactional->isEmpty()) {
            $routeParams['id']          = $transactional->getId();
            $showOptions['name']        = $transactional->getName();
            $showOptions['key']         = $transactional->getKey();
            $showOptions['mailSubject'] = $transactional->getMailSubject();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fa-plus',
                    'route' => 'zfcadmin/mailing/transactional/new',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(
                            message: 'txt-new-transactional-email'),
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fa-link',
                    'route' => 'zfcadmin/mailing/transactional/view',
                    'text'  => $showOptions[$show] ?? $transactional->getName(),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/mailing/transactional/edit',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(
                            message: 'txt-edit-transactional-email'),
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(link: Link::fromArray(params: $linkParams));
    }
}
