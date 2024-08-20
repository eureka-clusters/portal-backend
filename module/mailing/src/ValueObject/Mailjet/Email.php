<?php

declare(strict_types=1);

namespace Mailing\ValueObject\Mailjet;

use function count;

final readonly class Email
{
    public function __construct(private array $from, private array $to, private array $cc, private array $bcc, private string $subject, private string $textPart, private string $htmlPart, private string $customID, private string $eventPayload, private ?array $replyTo = null, private string $trackOpens = 'enabled', private string $trackClicks = 'enabled', private string $customCampaign = '', private array $attachments = [], private array $inlinedAttachments = [], private array $headers = [])
    {
    }

    public function isValid(): bool
    {
        return $this->isInvalidReasons() === [];
    }

    public function isInvalidReasons(): array
    {
        $invalidReasons = [];

        if ($this->from === []) {
            $invalidReasons[] = 'No sender given';
        }

        if ($this->to === []) {
            $invalidReasons[] = 'No to given';
        }

        if ('' === $this->subject) {
            $invalidReasons[] = 'No subject given';
        }

        if ('' === $this->htmlPart) {
            $invalidReasons[] = 'No content given';
        }

        return $invalidReasons;
    }

    public function toArray(): array
    {
        $return = [
            'From'        => $this->from,
            'To'          => $this->to,
            'Subject'     => $this->subject,
            'TextPart'    => $this->textPart,
            'HTMLPart'    => $this->htmlPart,
            'TrackOpens'  => $this->trackOpens,
            'TrackClicks' => $this->trackClicks,
        ];

        if ($this->cc !== []) {
            $return['Cc'] = $this->cc;
        }

        if ($this->bcc !== []) {
            $return['Bcc'] = $this->bcc;
        }

        if (null !== $this->replyTo) {
            $return['ReplyTo'] = $this->replyTo;
        }

        if (null !== $this->customID) {
            $return['CustomID'] = $this->customID;
        }

        if (null !== $this->eventPayload) {
            $return['EventPayload'] = $this->eventPayload;
        }

        if (null !== $this->customCampaign) {
            $return['CustomCampaign'] = $this->customCampaign;
        }

        if ($this->headers !== []) {
            $return['Headers'] = $this->headers;
        }

        if ($this->attachments !== []) {
            $return['Attachments'] = $this->attachments;
        }

        if ($this->inlinedAttachments !== []) {
            $return['InlinedAttachments'] = $this->inlinedAttachments;
        }

        return $return;
    }
}
