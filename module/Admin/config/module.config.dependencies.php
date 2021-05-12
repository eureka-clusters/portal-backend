<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

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
            'ControllerPluginManager'
        ],
        Service\QueueService::class => [
            EntityManager::class,
            TranslatorInterface::class
        ],
        Service\AdminService::class => [
            EntityManager::class,
            TranslatorInterface::class
        ],
        Service\ApiService::class   => [
            EntityManager::class,
            TranslatorInterface::class
        ],

    ]
];
