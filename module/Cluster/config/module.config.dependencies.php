<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster;

use Doctrine\ORM\EntityManager;
use General\Service\GeneralService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Program\Service\CallService;
use Project\Provider\ProjectProvider;
use Project\Service\ProjectService;

return [
    ConfigAbstractFactory::class => [
        Command\UpdateProject::class              => [
            Options\ModuleOptions::class,
            ProjectService::class,
            CallService::class,
            ProjectProvider::class
        ],
        // Controllers
        Controller\Admin\ClusterController::class => [
            Service\ClusterService::class,
            Service\FormService::class,
            GeneralService::class,
            TranslatorInterface::class
        ],
        Controller\ImageController::class         => [
            Service\ClusterService::class
        ],
        Service\ClusterService::class             => [
            EntityManager::class
        ],
        Service\StatisticsService::class          => [
            EntityManager::class
        ],
        Form\ClusterForm::class                   => [
            EntityManager::class
        ]
    ]
];
