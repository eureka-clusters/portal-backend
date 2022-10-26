<?php

declare(strict_types=1);

namespace Mailing;

use Admin\Helper\GetAzureAccessToken;
use Admin\Service\SelectionService;
use Admin\Service\SelectionUserService;
use Admin\Service\UserService;
use Application\Service\FormService;
use Deeplink\Service\DeeplinkService;
use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Mailing\Command\FlushQueue;
use Mailing\Command\SendDistributionLists;
use Mailing\Command\SendQueue;
use Mailing\Controller\AttachmentController;
use Mailing\Controller\ConsoleController;
use Mailing\Controller\DistributionList\DetailsController;
use Mailing\Controller\EmailController;
use Mailing\Controller\JsonController;
use Mailing\Controller\MailerController;
use Mailing\Controller\Mailing\ManagerController;
use Mailing\Controller\Mailing\UserController;
use Mailing\Controller\SenderController;
use Mailing\Controller\TemplateController;
use Mailing\Controller\TransactionalController;
use Mailing\Service\DistributionListService;
use Mailing\Service\EmailService;
use Mailing\Service\GraphMailService;
use Mailing\Service\MailerService;
use Mailing\Service\MailingService;

return [
    ConfigAbstractFactory::class => [
        GraphMailService::class => [
            GetAzureAccessToken::class
        ],
        FlushQueue::class => [
            MailingService::class,
        ],
        SendQueue::class => [
            MailingService::class,
            EmailService::class,
        ],
        SendDistributionLists::class => [
            DistributionListService::class
        ],
        AttachmentController::class => [
            MailingService::class,
            TranslatorInterface::class,
        ],
        ConsoleController::class => [
            MailingService::class,
            EmailService::class,
        ],
        EmailController::class => [
            MailingService::class,
            EntityManager::class,
        ],
        UserController::class => [
            MailingService::class,
        ],
        ManagerController::class => [
            MailingService::class,
            EmailService::class,
            UserService::class,
            DeeplinkService::class,
            SelectionService::class,
            FormService::class,
            TranslatorInterface::class,
        ],
        Controller\DistributionList\ManagerController::class => [
            DistributionListService::class,
            EmailService::class,
            UserService::class,
            SelectionService::class,
            FormService::class,
            TranslatorInterface::class
        ],
        DetailsController::class => [
            DistributionListService::class,
            EmailService::class,
            UserService::class,
            TranslatorInterface::class,
        ],
        Controller\DistributionList\UserController::class => [
            DistributionListService::class,
        ],
        JsonController::class => [
            MailingService::class,
            EmailService::class,
            TranslatorInterface::class,
        ],
        SenderController::class => [
            MailingService::class,
            FormService::class,
            TranslatorInterface::class,
        ],
        TemplateController::class => [
            MailingService::class,
            FormService::class,
            TranslatorInterface::class,
        ],
        MailerController::class => [
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
        MailingService::class => [
            EntityManager::class,
            SelectionUserService::class,
        ],
        MailerService::class => [
            EntityManager::class,
        ],
        DistributionListService::class => [
            EntityManager::class,
            SelectionUserService::class,
            EmailService::class,
            'ControllerPluginManager',
        ],
    ],
];
