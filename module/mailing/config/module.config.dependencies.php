<?php

declare(strict_types=1);

namespace Mailing;

use Application\Service\FormService;
use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Mailing\Controller\EmailController;
use Mailing\Controller\MailerController;
use Mailing\Controller\SenderController;
use Mailing\Controller\TemplateController;
use Mailing\Controller\TransactionalController;
use Mailing\Service\EmailService;
use Mailing\Service\MailerService;
use Mailing\Service\MailingService;

return [
    ConfigAbstractFactory::class => [
        EmailController::class         => [
            MailingService::class
        ],
        SenderController::class        => [
            MailingService::class,
            FormService::class,
            TranslatorInterface::class,
        ],
        TemplateController::class      => [
            MailingService::class,
            FormService::class,
            TranslatorInterface::class,
        ],
        MailerController::class        => [
            MailerService::class,
            EmailService::class,
            FormService::class,
            TranslatorInterface::class,
        ],
        TransactionalController::class => [
            MailingService::class,
            EmailService::class,
            FormService::class,
            TranslatorInterface::class,
        ],
        MailingService::class          => [
            EntityManager::class,
        ],
        MailerService::class           => [
            EntityManager::class,
        ],
    ],
];
