<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Api;

use Admin\Service\UserService;
use Api\Options\ModuleOptions;
use Api\V1\Rest;
use Cluster\Service\StatisticsService;
use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Rest\UpdateResource\ProjectListener::class      => [
            StatisticsService::class,
            EntityManager::class
        ],
        Rest\StatisticsResource\FacetsListener::class   => [
            StatisticsService::class,
            UserService::class
        ],
        Rest\StatisticsResource\ResultsListener::class  => [
            StatisticsService::class,
            UserService::class
        ],
        Rest\StatisticsResource\DownloadListener::class => [
            StatisticsService::class,
            UserService::class,
            TranslatorInterface::class
        ],
        Service\OAuthService::class  => [
            EntityManager::class,
            TranslatorInterface::class,
            ModuleOptions::class
        ],
    ]
];
