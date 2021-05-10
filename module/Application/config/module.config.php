<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Application;

use Doctrine\Common\Cache\RedisCache;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Laminas\Stdlib;

$config = [
    'service_manager' => [
        'aliases'   => [
            'doctrine.cache.application_cache' => RedisCache::class,
            'paportal_pdo_adapter'             => Authentication\OAuth2\Adapter\PdoAdapter::class,
        ],
        'factories' => [
            RedisCache::class                               => Factory\RedisFactory::class,
            Authentication\OAuth2\Adapter\PdoAdapter::class => Authentication\Factory\PdoAdapterFactory::class,
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

foreach (Stdlib\Glob::glob(__DIR__ . '/module.config.{,*}.php', Stdlib\Glob::GLOB_BRACE) as $file) {
    $config = Stdlib\ArrayUtils::merge($config, include $file);
}

return $config;
