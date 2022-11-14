<?php

declare(strict_types=1);

namespace Mailing\Service;

use Application\Service\AbstractService;
use Mailing\Entity\Mailer;

use function count;

class MailerService extends AbstractService
{
    public function findMailerById(int $id): ?Mailer
    {
        return $this->entityManager->getRepository(entityName: Mailer::class)->find(id: $id);
    }

    public function canDeleteMailer(Mailer $mailer): bool
    {
        $cannotDeleteMailer = [];

        if (! $mailer->getTransactional()->isEmpty()) {
            $cannotDeleteMailer[] = 'This mailer email has transactional mailings';
        }

        return count($cannotDeleteMailer) === 0;
    }

    public function getFormFieldsByService(int $service): array
    {
        return match ($service) {
            Mailer::MAILER_SERVICE_SMTP => ['hostname', 'username', 'password', 'port', 'ssl'],
            Mailer::MAILER_SERVICE_MAILJET => ['username', 'password'],
            Mailer::MAILER_SERVICE_SENDGRID => ['sendGridApiKey'],
            default => [],
        };
    }

    public function getRequiredFormFieldsByService(int $service): array
    {
        return match ($service) {
            Mailer::MAILER_SERVICE_SMTP => ['hostname', 'port'],
            Mailer::MAILER_SERVICE_MAILJET => ['username', 'password'],
            Mailer::MAILER_SERVICE_SENDGRID => ['sendGridApiKey'],
            default => [],
        };
    }
}
