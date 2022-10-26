<?php

declare(strict_types=1);

namespace Mailing\Service;

use Mailing\Entity\Mailer;
use Application\Service\AbstractService;
use Mailing\Entity;

use function count;

class MailerService extends AbstractService
{
    public function findMailerById(int $id): ?Mailer
    {
        return $this->entityManager->getRepository(Mailer::class)->find($id);
    }

    public function canDeleteMailer(Mailer $mailer): bool
    {
        $cannotDeleteMailer = [];

        if (!$mailer->getTransactional()->isEmpty()) {
            $cannotDeleteMailer[] = 'This mailer email has transactional mailings';
        }
        if (!$mailer->getMailing()->isEmpty()) {
            $cannotDeleteMailer[] = 'This mailer email has mailings';
        }

//        if (!$mailer->getEmailMessage()->isEmpty()) {
//            $cannotDeleteMailer[] = 'This mailer email has email messages';
//        }

        return count($cannotDeleteMailer) === 0;
    }

    public function getFormFieldsByService(int $service): array
    {
        return match ($service) {
            Mailer::MAILER_SERVICE_SMTP => ['hostname', 'username', 'password', 'port', 'ssl'],
            Mailer::MAILER_SERVICE_GRAPH => ['graphTenantId', 'graphClientId', 'graphClientSecret'],
            Mailer::MAILER_SERVICE_MAILJET => ['username', 'password'],
            Mailer::MAILER_SERVICE_SENDGRID => ['sendGridApiKey'],
            default => [],
        };
    }

    public function getRequiredFormFieldsByService(int $service): array
    {
        return match ($service) {
            Mailer::MAILER_SERVICE_SMTP => ['hostname', 'port'],
            Mailer::MAILER_SERVICE_GRAPH => ['graphTenantId', 'graphClientId', 'graphClientSecret'],
            Mailer::MAILER_SERVICE_MAILJET => ['username', 'password'],
            Mailer::MAILER_SERVICE_SENDGRID => ['sendGridApiKey'],
            default => [],
        };
    }
}
