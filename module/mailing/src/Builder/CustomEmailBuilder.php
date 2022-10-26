<?php

declare(strict_types=1);

namespace Mailing\Builder;

use Mailing\Service\MailingService;

final class CustomEmailBuilder extends EmailBuilder
{
    private ?string $customSubject = null;
    private ?string $customBody = null;

    public function __construct(MailingService $mailingService)
    {
        parent::__construct(mailingService: $mailingService);
    }

    public function renderEmail(): void
    {
        $this->emailCampaign = 'Custom email';

        if (null !== $this->customSubject) {
            $this->renderSubject(mailSubject: $this->customSubject);
        }
        if (null !== $this->customBody) {
            $this->renderBody(bodyText: $this->customBody);
        }
    }

    public function setSubject(string $subject): CustomEmailBuilder
    {
        $this->customSubject = $subject;

        return $this;
    }

    public function setBody(string $body): CustomEmailBuilder
    {
        $this->customBody = $body;

        return $this;
    }
}
