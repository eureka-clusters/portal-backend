<?php

declare(strict_types=1);

namespace Mailing\Service;

use Application\Service\AbstractService;
use Mailing\Entity\EmailMessage;
use Mailing\Entity\Sender;
use Mailing\Entity\Template;
use Mailing\Entity\Transactional;

use function count;

class MailingService extends AbstractService
{
    public function findTransactionalByKey(string $key): ?Transactional
    {
        return $this->entityManager->getRepository(entityName: Transactional::class)->findOneBy(
            criteria: [
                'key' => $key,
            ]
        );
    }

    public function findDefaultSender(): Sender
    {
        return $this->entityManager->find(className: Sender::class, id: Sender::SENDER_DEFAULT);
    }

    public function findLoggedInUserSender(): Sender
    {
        return $this->entityManager->find(className: Sender::class, id: Sender::SENDER_LOGGED_IN_USER);
    }

    public function findOwnerSender(): Sender
    {
        return $this->entityManager->find(className: Sender::class, id: Sender::SENDER_OWNER);
    }

    public function findDefaultTemplate(): Template
    {
        return $this->entityManager->find(className: Template::class, id: Template::TEMPLATE_DEFAULT);
    }

    public function canDeleteTransactional(Transactional $transactional): bool
    {
        $cannotDeleteTransactional = [];

        if ($transactional->isLocked()) {
            $cannotDeleteTransactional[] = 'This transactional email is locked';
        }

        return count($cannotDeleteTransactional) === 0;
    }

    public function canDeleteSender(Sender $sender): bool
    {
        $cannotDeleteSenderReasons = [];

        if ($sender->isDefault()) {
            $cannotDeleteSenderReasons[] = 'This sender is default';
        }

        if ($sender->isOwner()) {
            $cannotDeleteSenderReasons[] = 'This sender is for owner of mailings';
        }

        if ($sender->isLoggedInUser()) {
            $cannotDeleteSenderReasons[] = 'This sender is for the logged in user';
        }

        if (! $sender->getTransactional()->isEmpty()) {
            $cannotDeleteSenderReasons[] = 'Sender has transactional emails';
        }

        return count($cannotDeleteSenderReasons) === 0;
    }

    public function canDeleteTemplate(Template $template): bool
    {
        $cannotDeleteTemplateReasons = [];

        if ($template->isDefault()) {
            $cannotDeleteTemplateReasons[] = 'This template is default';
        }

        if (! $template->getTransactional()->isEmpty()) {
            $cannotDeleteTemplateReasons[] = 'Template has transactional emails';
        }

        return count($cannotDeleteTemplateReasons) === 0;
    }

    public function findEmailMessageByIdentifier(string $identifier): ?EmailMessage
    {
        return $this->entityManager->getRepository(entityName: EmailMessage::class)
            ->findOneBy(criteria: ['identifier' => $identifier]);
    }
}
