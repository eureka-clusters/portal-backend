<?php

declare(strict_types=1);

namespace Admin;

use Admin\Controller\OAuth2\ClientController;
use Admin\Controller\OAuth2\ScopeController;
use Admin\Controller\RoleController;
use Admin\Controller\UserController;
use Admin\Service\AdminService;
use Admin\Service\ApiService;
use Admin\Service\OAuth2Service;
use Admin\Service\UserService;
use Application\Service\FormService;
use Doctrine\ORM\EntityManager;
use Laminas\Authentication\AuthenticationService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        UserService::class => [
            EntityManager::class,
            TranslatorInterface::class,
            AdminService::class,
            'ControllerPluginManager',
        ],
        AdminService::class => [
            EntityManager::class,
            TranslatorInterface::class,
        ],
        ApiService::class => [
            EntityManager::class,
            TranslatorInterface::class,
        ],
        UserController::class => [
            AdminService::class,
            UserService::class,
            'Config',
            EntityManager::class,
            AuthenticationService::class,
            TranslatorInterface::class,
        ],
        RoleController::class => [
            AdminService::class,
            FormService::class,
            TranslatorInterface::class,
        ],
        ClientController::class => [
            OAuth2Service::class,
            EntityManager::class,
            TranslatorInterface::class,
        ],
        ScopeController::class => [
            OAuth2Service::class,
            TranslatorInterface::class,
        ],
    ],
];
