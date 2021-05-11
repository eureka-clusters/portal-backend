<?php
/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Api;

use Api\V1\Rest;
use Cluster\Service\ClusterService;
use Cluster\Service\StatisticsService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

$config = [
    'router'                     => [
        'routes' => [
            'api' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/api',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'navigation'                 => [
        'admin' => [
            'tools' => [
                'pages' => [
                    'api' => [
                        'order' => 110,
                        'label' => _('txt-api-tools'),
                        'route' => 'api',
                    ],
                ],
            ],
        ],
    ],
    'doctrine'                   => [
        'driver' => [
            'api_annotation_driver' => [
                'class' => AnnotationDriver::class,
                'paths' => [
                    0 => __DIR__ . '/../src/Entity/',
                ],
            ],
            'orm_default'           => [
                'drivers' => [
                    'Api\\Entity' => 'api_annotation_driver',
                ],
            ],
        ],
    ],
    ConfigAbstractFactory::class => [
        Rest\UpdateResource\ProjectListener::class      => [
            StatisticsService::class,
            EntityManager::class
        ],
        Rest\StatisticsResource\FacetsListener::class   => [
            StatisticsService::class,
            ClusterService::class
        ],
        Rest\StatisticsResource\ResultsListener::class  => [
            StatisticsService::class,
            ClusterService::class
        ],
        Rest\StatisticsResource\DownloadListener::class => [
            StatisticsService::class,
            ClusterService::class,
            TranslatorInterface::class,
        ],
    ],
    'service_manager'            => [
        'factories' => [
            Rest\UpdateResource\ProjectListener::class      => ConfigAbstractFactory::class,
            Rest\StatisticsResource\FacetsListener::class   => ConfigAbstractFactory::class,
            Rest\StatisticsResource\ResultsListener::class  => ConfigAbstractFactory::class,
            Rest\StatisticsResource\DownloadListener::class => ConfigAbstractFactory::class,
        ],
    ],
];

return array_merge_recursive(
    $config,
    include 'module.config.php'
);
