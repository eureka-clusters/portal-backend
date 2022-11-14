<?php

declare(strict_types=1);

namespace Application;

use Admin\Service\OAuth2Service;
use Admin\Service\UserService;
use Application\Authentication\Storage\AuthenticationStorage;
use Application\Controller\IndexController;
use Application\Controller\OAuth2Controller;
use Application\Event\InjectAclInNavigation;
use Application\Event\SetTitle;
use Application\Session\SaveHandler\DoctrineGateway;
use Doctrine\ORM\EntityManager;
use Jield\Authorize\Service\AuthorizeService;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        IndexController::class       => [
            OAuth2Service::class,
        ],
        OAuth2Controller::class      => [
            UserService::class,
            OAuth2Service::class,
            'Config',
        ],
        InjectAclInNavigation::class => [
            AuthorizeService::class,
            'Config',
        ],
        SetTitle::class              => [
            'ViewRenderer',
        ],
        AuthenticationService::class => [
            AuthenticationStorage::class,
        ],
        AuthenticationStorage::class => [
            DoctrineGateway::class,
            UserService::class,
        ],
        DoctrineGateway::class       => [
            EntityManager::class,
            'Config',
        ],
    ],
];
