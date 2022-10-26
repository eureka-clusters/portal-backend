<?php

declare(strict_types=1);

namespace Mailing\View\Helper;

use Application\ValueObject\Link\Link;
use Application\View\Helper\AbstractLink;
use Mailing\Entity\Mailer;

final class MailerLink extends AbstractLink
{
    public function __invoke(
        ?Mailer $mailer = null,
        string $action = 'view',
        string $show = 'text',
        ?int $service = null
    ): string {
        $linkParams = [];
        $mailer ??= new Mailer();

        $routeParams = [];
        $showOptions = [];

        if (!$mailer->isEmpty()) {
            $routeParams['id'] = $mailer->getId();
            $showOptions['name'] = $mailer->getName();
        }

        $serviceName = null;
        if (null !== $service) {
            $routeParams['service'] = $service;
            $serviceName = Mailer::getServicesArray()[$service] ?? 'txt-unknown';
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon' => 'fa-plus',
                    'route' => 'zfcadmin/mailing/mailer/new',
                    'text' => $showOptions[$show] ?? sprintf(
                            $this->translator->translate(message: 'txt-new-mailer-of-type-%s'),
                            $this->translator->translate(message: $serviceName)
                        ),
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon' => 'fa-link',
                    'route' => 'zfcadmin/mailing/mailer/view',
                    'text' => $showOptions[$show] ?? $this->translator->translate(message: 'txt-view-mailer'),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon' => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/mailing/mailer/edit',
                    'text' => $showOptions[$show] ?? $this->translator->translate(message: 'txt-edit-mailer'),
                ];
                break;
        }

        $linkParams['action'] = $action;
        $linkParams['show'] = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(link: Link::fromArray(params: $linkParams));
    }
}
