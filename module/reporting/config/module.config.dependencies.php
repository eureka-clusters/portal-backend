<?php

declare(strict_types=1);

namespace Reporting;

use Admin\Service\OAuth2Service;
use Application\Service\FormService;
use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Reporting\Controller\DownloadController;
use Reporting\Controller\ReportingController;
use Reporting\Controller\StorageLocationController;
use Reporting\Service\StorageLocationService;

return [
    ConfigAbstractFactory::class => [
        StorageLocationController::class => [
            StorageLocationService::class,
            FormService::class,
            TranslatorInterface::class
        ],
        ReportingController::class       => [
            StorageLocationService::class,
        ],
        DownloadController::class        => [
            StorageLocationService::class,
        ],
        StorageLocationService::class    => [
            EntityManager::class,
            OAuth2Service::class,
        ]
    ]
];
