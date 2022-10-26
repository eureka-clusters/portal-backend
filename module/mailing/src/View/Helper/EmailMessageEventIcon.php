<?php

declare(strict_types=1);

namespace Mailing\View\Helper;

use Mailing\Entity\EmailMessage;

use function sprintf;

final class EmailMessageEventIcon
{
    public function __invoke(EmailMessage $message): string
    {
        $color = match ($message->getLatestEvent()) {
            'blocked' => '#000',
            'bounce' => '#ff9600',
            'click' => '#14a400',
            'open' => '#97d600',
            'sent', 'sent_to_mailjet' => '#dfdfdf',
            'spam' => '#ff0000',
            'unsub' => '#1dc7ff',
            default => 'var(--bs-black-400)',
        };
        return sprintf(
            '<i class="fa fa-circle-thin" style="color: %s" title="%s on %s"></i>',
            $color,
            htmlentities($message->getLatestEvent()),
            $message->getDateLatestEvent()->format('d-m-Y H:i:s')
        );
    }
}
