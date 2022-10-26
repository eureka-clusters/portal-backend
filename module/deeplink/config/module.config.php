<?php

declare(strict_types=1);

use Application\Factory\InvokableFactory;
use Application\View\Factory\LinkHelperFactory;
use Deeplink\Controller\DeeplinkController;
use Deeplink\Controller\TargetController;
use Deeplink\InputFilter\TargetFilter;
use Deeplink\Navigation\Invokable\TargetLabel;
use Deeplink\Service\DeeplinkService;
use Deeplink\View\Helper\CanAssemble;
use Deeplink\View\Helper\Deeplink\TargetLink;
use Deeplink\View\Helper\DeeplinkLink;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;

$config = [
    'controllers'     => [
        'factories' => [
            DeeplinkController::class => ConfigAbstractFactory::class,
            TargetController::class   => ConfigAbstractFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            DeeplinkService::class => ConfigAbstractFactory::class,
            TargetFilter::class    => ConfigAbstractFactory::class,
            TargetLabel::class     => InvokableFactory::class,
        ],
    ],
    'view_manager'    => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
    'view_helpers'    => [
        'aliases'   => [
            'canAssemble'        => CanAssemble::class,
            'deeplinkLink'       => DeeplinkLink::class,
            'deeplinkTargetLink' => TargetLink::class,
        ],
        'factories' => [
            CanAssemble::class  => ConfigAbstractFactory::class,
            DeeplinkLink::class => LinkHelperFactory::class,
            TargetLink::class   => LinkHelperFactory::class,
        ],
    ],
    'doctrine'        => [
        'driver' => [
            'deeplink_attribute_driver' => [
                'class' => AttributeDriver::class,
                'paths' => [
                    __DIR__ . '/../src/Entity/',
                ],
            ],
            'orm_default_chain'         => [
                'drivers' => [
                    'Deeplink\Entity' => 'deeplink_attribute_driver',
                ],
            ],
        ],
    ],
];

foreach (Glob::glob(pattern: __DIR__ . '/module.config.{,*}.php', flags: Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge(a: $config, b: include $file);
}

return $config;
