<?php

declare(strict_types=1);

namespace Admin\View\Helper;

use Admin\Acl;
use Admin\Entity\User;
use Application\ValueObject\Link\Link;
use Application\View\Helper\AbstractLink;

use function sprintf;

final class UserLink extends AbstractLink
{
    public function __invoke(?User $user = null, string $action = 'view', string $show = 'name'): string
    {
        $linkParams = [];
        $user ??= new User();

        if (! $this->hasAccess($user, Acl\Assertion\User::class, $action)) {
            if ($action === 'view' && $show === 'initials') {
                return $user->parseInitials();
            }

            if ($action === 'view' && $show !== 'icon') {
                return $user->parseFullName();
            }

            return '';
        }

        $routeParams = [];
        $showOptions = [];

        if (! $user->isEmpty()) {
            $routeParams['id']       = $user->getId();
            $showOptions['name']     = $user->parseFullName();
            $showOptions['email']    = $user->getEmail();
            $showOptions['initials'] = $user->parseInitials();
        }

        switch ($action) {
            case 'view':
                $linkParams = [
                    'icon'  => 'fa-user',
                    'route' => 'user/view',
                    'text'  => $showOptions[$show] ?? $user->parseFullName(),
                ];
                break;
            case 'edit-profile':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'user/edit',
                    'text'  => $showOptions[$show] ?? $this->translator->translate('txt-edit-profile'),
                ];
                break;
            case 'view-admin':
                $linkParams = [
                    'icon'  => 'fa-user-circle-o',
                    'route' => 'zfcadmin/user/view',
                    'text'  => $showOptions[$show] ?? sprintf(
                        $this->translator->translate('txt-view-user-%s-in-admin'),
                        $user->parseFullName()
                    ),
                ];
                break;
            case 'new':
                $linkParams = [
                    'icon'  => 'fa-plus',
                    'route' => 'zfcadmin/user/new',
                    'text'  => $showOptions[$show] ?? $this->translator->translate('txt-create-user'),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/user/edit',
                    'text'  => $showOptions[$show] ?? $this->translator->translate('txt-edit-user'),
                ];
                break;
            case 'impersonate':
                $linkParams = [
                    'icon'  => 'fa-user-secret',
                    'route' => 'zfcadmin/user/impersonate',
                    'text'  => $showOptions[$show] ?? $this->translator->translate('txt-impersonate'),
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
