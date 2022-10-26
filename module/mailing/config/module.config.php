<?php

declare(strict_types=1);

namespace Mailing;

use Application\Factory\InputFilterFactory;
use Application\Factory\InvokableFactory;
use Application\View\Factory\LinkHelperFactory;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;
use Mailing\Controller\EmailController;
use Mailing\Controller\MailerController;
use Mailing\Controller\SenderController;
use Mailing\Controller\TemplateController;
use Mailing\Controller\TransactionalController;
use Mailing\InputFilter\MailerFilter;
use Mailing\InputFilter\SenderFilter;
use Mailing\InputFilter\TemplateFilter;
use Mailing\InputFilter\TransactionalFilter;
use Mailing\Navigation\Invokable\EmailMessageLabel;
use Mailing\Navigation\Invokable\MailerLabel;
use Mailing\Navigation\Invokable\SenderLabel;
use Mailing\Navigation\Invokable\TemplateLabel;
use Mailing\Navigation\Invokable\TransactionalLabel;
use Mailing\Service\EmailService;
use Mailing\Service\MailerService;
use Mailing\Service\MailingService;
use Mailing\View\Helper\EmailMessageEventIcon;
use Mailing\View\Helper\EmailMessageLink;
use Mailing\View\Helper\MailerLink;
use Mailing\View\Helper\SenderLink;
use Mailing\View\Helper\TemplateLink;
use Mailing\View\Helper\TransactionalLink;

$config = [
    'controllers' => [
        'factories' => [
            EmailController::class => ConfigAbstractFactory::class,
            SenderController::class => ConfigAbstractFactory::class,
            MailerController::class => ConfigAbstractFactory::class,
            TemplateController::class => ConfigAbstractFactory::class,
            TransactionalController::class => ConfigAbstractFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            MailingService::class => ConfigAbstractFactory::class,
            MailerService::class => ConfigAbstractFactory::class,

            TemplateFilter::class => InputFilterFactory::class,
            MailerFilter::class => InputFilterFactory::class,
            SenderFilter::class => InputFilterFactory::class,
            TransactionalFilter::class => InputFilterFactory::class,

            EmailMessageLabel::class => InvokableFactory::class,
            TransactionalLabel::class => InvokableFactory::class,
            MailerLabel::class => InvokableFactory::class,
            SenderLabel::class => InvokableFactory::class,
            TemplateLabel::class => InvokableFactory::class,
            EmailService::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers' => [
        'aliases' => [
            'mailerLink' => MailerLink::class,
            'transactionalLink' => TransactionalLink::class,
            'mailingTemplateLink' => TemplateLink::class,
            'senderLink' => SenderLink::class,
            'emailMessageLink' => EmailMessageLink::class,
            'emailMessageEventIcon' => EmailMessageEventIcon::class,
        ],
        'invokables' => [
            EmailMessageEventIcon::class,
        ],
        'factories' => [
            MailerLink::class => LinkHelperFactory::class,
            TransactionalLink::class => LinkHelperFactory::class,
            TemplateLink::class => LinkHelperFactory::class,
            SenderLink::class => LinkHelperFactory::class,
            EmailMessageLink::class => LinkHelperFactory::class,
        ],
    ],
    'doctrine' => [
        'driver' => [
            'mailing_attribute_driver' => [
                'class' => AttributeDriver::class,
                'paths' => [
                    __DIR__ . '/../src/Entity/',
                ],
            ],
            'orm_default_chain' => [
                'drivers' => [
                    'Mailing\Entity' => 'mailing_attribute_driver',
                ],
            ],
        ],
    ],
];

foreach (Glob::glob(__DIR__ . '/module.config.{,*}.php', Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge($config, include $file);
}

return $config;
