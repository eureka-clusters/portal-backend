<?php

declare(strict_types=1);

namespace Application;

use Application\Controller\OAuth2Controller;
use Application\Controller\IndexController;
use Application\Factory\RedisFactory;
use Laminas\Stdlib\Glob;
use Laminas\Stdlib\ArrayUtils;
use Application\Authentication\Factory\PdoAdapterFactory;
use Application\Factory\ModuleOptionsFactory;
use Application\Options\ModuleOptions;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Laminas\ApiTools\MvcAuth\Factory\AuthenticationServiceFactory;
use Laminas\ApiTools\OAuth2\Adapter\PdoAdapter;
use Laminas\Authentication\AuthenticationService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\I18n\Translator\TranslatorServiceFactory;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\Stdlib;

$config = [
    'controllers'     => [
        'factories'  => [
            OAuth2Controller::class => ConfigAbstractFactory::class,
        ],
        'invokables' => [
            IndexController::class,
        ],
    ],
    'service_manager' => [
        'aliases'   => [
            'doctrine.cache.application_cache' => RedisCache::class,
        ],
        'factories' => [
            RedisCache::class            => RedisFactory::class,
            PdoAdapter::class            => PdoAdapterFactory::class,
            TranslatorInterface::class   => TranslatorServiceFactory::class,
            AuthenticationService::class => AuthenticationServiceFactory::class,
        ],
    ],
    'translator'      => [
        'locale'                    => 'en_GB',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../../../style/language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
    'view_manager'    => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_path_stack'      => [
            'application' => __DIR__ . '/../view',
        ],
        'template_map'             => require __DIR__ . '/../template_map.php',
        'strategies'               => [
            'ViewJsonStrategy',
        ],
    ],
    'doctrine'        => [
        'driver'        => [
            'orm_default' => [
                'class' => MappingDriverChain::class,
            ],
        ],
        'entitymanager' => [
            'orm_default' => [
                'connection'    => 'orm_default',
                'configuration' => 'orm_default',
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'metadata_cache'   => 'application_cache',
                'query_cache'      => 'application_cache',
                'result_cache'     => 'application_cache',
                'hydration_cache'  => 'application_cache',
                'generate_proxies' => false,
            ],
        ],
    ],
];

foreach (Glob::glob(__DIR__ . '/module.config.{,*}.php', Glob::GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge($config, include $file);
}

return $config;
