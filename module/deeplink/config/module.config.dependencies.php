<?php

declare(strict_types=1);

namespace Deeplink;

use Admin\Service\AdminService;
use Application\Service\FormService;
use Deeplink\Controller\DeeplinkController;
use Deeplink\Controller\TargetController;
use Deeplink\InputFilter\TargetFilter;
use Deeplink\Service\DeeplinkService;
use Deeplink\View\Helper\CanAssemble;
use Doctrine\ORM\EntityManager;
use Laminas\Authentication\AuthenticationService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        // Controllers
        DeeplinkController::class => [
            DeeplinkService::class,
            AuthenticationService::class,
            AdminService::class,
        ],
        TargetController::class   => [
            DeeplinkService::class,
            FormService::class,
            TreeRouteStack::class,
            TranslatorInterface::class,
        ],
        DeeplinkService::class    => [
            EntityManager::class,
            'ViewHelperManager',
        ],
        TargetFilter::class       => [
            EntityManager::class,
            TreeRouteStack::class,
        ],
        CanAssemble::class        => [
            'Router',
        ],
    ],
];
