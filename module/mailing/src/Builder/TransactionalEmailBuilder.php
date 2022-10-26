<?php

declare(strict_types=1);

namespace Mailing\Builder;

use Deeplink\Service\DeeplinkService;
use Laminas\Authentication\AuthenticationService;
use Mailing\Entity\DistributionList\User;
use Mailing\Entity\Transactional;
use Mailing\Service\MailingService;

final class TransactionalEmailBuilder extends EmailBuilder
{
    private readonly Transactional $transactional;

    public function __construct(
        Transactional $transactional,
        MailingService $mailingService,
        DeeplinkService $deeplinkService,
        AuthenticationService $authenticationService
    ) {
        parent::__construct(
            mailingService: $mailingService,
            deeplinkService: $deeplinkService
        );

        $this->setSender($transactional->getSender());

        if ($authenticationService->hasIdentity()) {
            $this->setReplyToUser($authenticationService->getIdentity());
        }

        $this->transactional = $transactional;
    }

    public function renderEmail(): void
    {
        $this->emailCampaign = $this->transactional->getKey();
        $this->template = $this->transactional->getTemplate();

        $this->renderSubject($this->transactional->getMailSubject());
        $this->renderBody($this->transactional->getMailHtml());
    }

    public function setDistributionListUser(User $distributionListUser): TransactionalEmailBuilder
    {
        $this->distributionListUser = $distributionListUser;
        $this->addUserTo($distributionListUser->getUser());

        return $this;
    }

}
