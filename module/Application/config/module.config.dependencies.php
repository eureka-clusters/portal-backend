<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Application;

use Admin\Service\AdminService;
use Admin\Service\UserService;
use Doctrine\ORM\EntityManager;
use Laminas\Authentication\AuthenticationService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        // Controllers
        Controller\SupportController::class                     => [
            AdminService::class
        ],
        Controller\TicketController::class                      => [
            TranslatorInterface::class,
            'Config'
        ],
        //Plugins
        Controller\Plugin\GetFilter::class                      => [
            'Application',
            UserService::class,
            AuthenticationService::class
        ],
        Controller\Plugin\Preferences::class                    => [
            'Application',
            AuthenticationService::class
        ],
        // Services
        Service\SetTitle::class                                 => [
            'ViewRenderer',
            'Config'
        ],
        View\UnauthorizedStrategy::class                        => [
            'BjyAuthorize\Config',
            AuthenticationService::class
        ],
        AuthenticationService::class                            => [
            Authentication\Storage\AuthenticationStorage::class
        ],
        Authentication\Storage\AuthenticationStorage::class     => [
            Session\SaveHandler\DoctrineGateway::class,
            UserService::class
        ],
        Authentication\OAuth2\Adapter\PdoAdapter::class         => [
            EntityManager::class
        ],
        Session\SaveHandler\DoctrineGateway::class              => [
            EntityManager::class,
            'Config'
        ],
        Provider\Identity\AuthenticationIdentityProvider::class => [
            AuthenticationService::class,
            AdminService::class
        ],
        Twig\DatabaseTwigLoader::class                          => [
            EntityManager::class
        ],
    ]
];
