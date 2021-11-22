<?php

declare(strict_types=1);

namespace Admin;

use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Service\UserService::class  => [
            EntityManager::class,
            TranslatorInterface::class,
            Service\AdminService::class,
            'ControllerPluginManager',
        ],
        Service\AdminService::class => [
            EntityManager::class,
            TranslatorInterface::class,
        ],
        Service\ApiService::class   => [
            EntityManager::class,
            TranslatorInterface::class,
        ],
    ],
];
