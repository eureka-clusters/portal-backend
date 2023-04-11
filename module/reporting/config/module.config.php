<?php

declare(strict_types=1);

namespace Reporting;

use Application\Factory\InputFilterFactory;
use Application\Factory\InvokableFactory;
use Application\View\Factory\LinkHelperFactory;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Jield\Export\Service\StorageLocationServiceInterface;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;
use Reporting\Controller\DownloadController;
use Reporting\Controller\ReportingController;
use Reporting\Controller\StorageLocationController;
use Reporting\InputFilter\StorageLocationFilter;
use Reporting\Navigation\Invokable\StorageLocationLabel;
use Reporting\Service\StorageLocationService;
use Reporting\View\Helper\StorageLocationLink;

$config = [
    'controllers'     => [
        'factories' => [
            StorageLocationController::class => ConfigAbstractFactory::class,
            ReportingController::class       => ConfigAbstractFactory::class,
            DownloadController::class        => ConfigAbstractFactory::class
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'service_manager' => [
        'aliases'   => [
            StorageLocationServiceInterface::class => StorageLocationService::class,
        ],
        'factories' => [
            StorageLocationService::class => ConfigAbstractFactory::class,
            StorageLocationFilter::class  => InputFilterFactory::class,
            StorageLocationLabel::class   => InvokableFactory::class,
        ],
    ],
    'view_helpers'    => [
        'aliases'   => [
            'storageLocationLink' => StorageLocationLink::class,
        ],
        'factories' => [
            StorageLocationLink::class => LinkHelperFactory::class,
        ],
    ],
    'doctrine'        => [
        'driver' => [
            'reporting_attribute_driver' => [
                'class' => AttributeDriver::class,
                'paths' => [
                    __DIR__ . '/../src/Entity/',
                ],
            ],
            'orm_default'                => [
                'drivers' => [
                    'Reporting\Entity' => 'reporting_attribute_driver',
                ],
            ],
        ],
    ],
];

foreach (Glob::glob(pattern: __DIR__ . '/module.config.{,*}.php', flags: Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge(a: $config, b: include $file);
}

return $config;
