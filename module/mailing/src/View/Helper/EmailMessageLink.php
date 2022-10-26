<?php

declare(strict_types=1);

namespace Mailing\View\Helper;

use Application\ValueObject\Link\Link;
use Application\View\Helper\AbstractLink;
use Mailing\Entity\EmailMessage;

final class EmailMessageLink extends AbstractLink
{
    public function __invoke(EmailMessage $emailMessage): string
    {
        $routeParams       = [];
        $routeParams['id'] = $emailMessage->getId();

        $linkParams = [
            'icon'  => 'fa-envelope-o',
            'route' => 'zfcadmin/mailing/email/view',
            'text'  => $emailMessage->getSubject(),
        ];

        $linkParams['action']      = 'view';
        $linkParams['show']        = 'text';
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(Link::fromArray($linkParams));
    }
}
