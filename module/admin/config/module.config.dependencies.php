<?php

declare(strict_types=1);

namespace Admin;

use Admin\Service\UserService;
use Admin\Service\AdminService;
use Admin\Service\ApiService;
use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        UserService::class  => [
            EntityManager::class,
            TranslatorInterface::class,
            AdminService::class,
            'ControllerPluginManager',
        ],
        AdminService::class => [
            EntityManager::class,
            TranslatorInterface::class,
        ],
        ApiService::class   => [
            EntityManager::class,
            TranslatorInterface::class,
        ],
    ],
];
