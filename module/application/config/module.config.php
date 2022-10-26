<?php

declare(strict_types=1);

namespace Application;

use Application\Authentication\Factory\PdoAdapterFactory;
use Application\Authentication\Storage\AuthenticationStorage;
use Application\Controller\IndexController;
use Application\Controller\OAuth2Controller;
use Application\Controller\Plugin\GetFilter;
use Application\Controller\Plugin\Preferences;
use Application\Event\InjectAclInNavigation;
use Application\Event\SetTitle;
use Application\Event\UpdateNavigation;
use Application\Factory\DoctrineCacheFactory;
use Application\Factory\InvokableFactory;
use Application\Factory\LaminasCacheFactory;
use Application\Factory\ModuleOptionsFactory;
use Application\Options\ModuleOptions;
use Application\Service\FormService;
use Application\Session\SaveHandler\DoctrineGateway;
use Application\Twig\StringDateExtension;
use Application\View\Helper\PaginationLink;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\Timestampable\TimestampableListener;
use Laminas\ApiTools\OAuth2\Adapter\PdoAdapter;
use Laminas\Authentication\AuthenticationService;
use Laminas\Cache\Storage\Adapter\Redis;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;
use Twig\Extension\DebugExtension;

$config = [
    'controllers' => [
        'factories' => [
            OAuth2Controller::class => ConfigAbstractFactory::class,
        ],
        'invokables' => [
            IndexController::class,
        ],
    ],
    'controller_plugins' => [
        'aliases' => [
            'getFilter' => GetFilter::class,
        ],
        'factories' => [
            GetFilter::class => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'BjyAuthorize\Cache' => Redis::class, //Map the bjy on the native cache
        ],
        'factories' => [
            'doctrine.cache.application_cache' => DoctrineCacheFactory::class,
            Authentication\Adapter\PdoAdapter::class => PdoAdapterFactory::class,
            Redis::class => LaminasCacheFactory::class,
            PdoAdapter::class => PdoAdapterFactory::class,
            TranslatorInterface::class => TranslatorServiceFactory::class,
            ModuleOptions::class => ModuleOptionsFactory::class,
            FormService::class => InvokableFactory::class,

            InjectAclInNavigation::class => ConfigAbstractFactory::class,
            SetTitle::class => ConfigAbstractFactory::class,
            UpdateNavigation::class => InvokableFactory::class,

            AuthenticationService::class => ConfigAbstractFactory::class,
            AuthenticationStorage::class => ConfigAbstractFactory::class,
            DoctrineGateway::class => ConfigAbstractFactory::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'translate' => Translate::class,
        ],
        'aliases' => [
            'paginationLink' => PaginationLink::class,
        ],
        'factories' => [
            PaginationLink::class => InvokableFactory::class,
        ],
    ],
    'translator' => [
        'locale' => 'en_GB',
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../../../style/language',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'zfctwig' => [
        'disable_zf_model' => false,
        'extensions' => [
            DebugExtension::class,
            StringDateExtension::class
        ],
        'environment_options' => [
            'cache' => __DIR__ . '/../../../data/twig/',
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/500',
        'template_path_stack' => [
            'application' => __DIR__ . '/../view',
        ],
        'template_map' => require __DIR__ . '/../template_map.php',
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'doctrine' => [
        'driver' => [
            'orm_default' => [
                'class' => MappingDriverChain::class,
            ],
        ],
        'entitymanager' => [
            'orm_default' => [
                'connection' => 'orm_default',
                'configuration' => 'orm_default',
            ],
        ],
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    TimestampableListener::class,
                    SluggableListener::class,
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'metadata_cache' => 'application_cache',
                'query_cache' => 'application_cache',
                'result_cache' => 'application_cache',
                'hydration_cache' => 'application_cache',
                'generate_proxies' => false,
            ],
        ],
    ],
];

foreach (Glob::glob(pattern: __DIR__ . '/module.config.{,*}.php', flags: Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge(a: $config, b: include $file);
}

return $config;
