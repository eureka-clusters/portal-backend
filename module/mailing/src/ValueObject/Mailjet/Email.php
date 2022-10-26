<?php

declare(strict_types=1);

namespace Mailing\ValueObject\Mailjet;

use function count;

final class Email
{
    public function __construct(private readonly array $from, private readonly array $to, private readonly array $cc, private readonly array $bcc, private readonly string $subject, private readonly string $textPart, private readonly string $htmlPart, private readonly string $customID, private readonly string $eventPayload, private readonly ?array $replyTo = null, private readonly string $trackOpens = 'enabled', private readonly string $trackClicks = 'enabled', private readonly string $customCampaign = '', private readonly array $attachments = [], private readonly array $inlinedAttachments = [], private readonly array $headers = [])
    {
    }

    public function isValid(): bool
    {
        return count($this->isInvalidReasons()) === 0;
    }

    public function isInvalidReasons(): array
    {
        $invalidReasons = [];

        if (count($this->from) === 0) {
            $invalidReasons[] = 'No sender given';
        }

        if (count($this->to) === 0) {
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

        if (count($this->cc) > 0) {
            $return['Cc'] = $this->cc;
        }

        if (count($this->bcc) > 0) {
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

        if (count($this->headers) > 0) {
            $return['Headers'] = $this->headers;
        }

        if (count($this->attachments) > 0) {
            $return['Attachments'] = $this->attachments;
        }

        if (count($this->inlinedAttachments) > 0) {
            $return['InlinedAttachments'] = $this->inlinedAttachments;
        }

        return $return;
    }
}
