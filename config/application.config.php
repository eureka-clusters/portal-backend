<?php

return [
    'modules'                 => [
        'Laminas\\Router',
        'Laminas\\InputFilter',
        'Laminas\\Hydrator',

        'Laminas\\ApiTools',
        'Laminas\\ApiTools\\Admin',
        'Laminas\\ApiTools\\Documentation',
        'Laminas\\ApiTools\\Documentation\\Swagger',
        'Laminas\\ApiTools\\ApiProblem',
        'Laminas\\ApiTools\\Configuration',
        'Laminas\\ApiTools\\OAuth2',
        'Laminas\\ApiTools\\MvcAuth',
        'Laminas\\ApiTools\\Hal',
        'Laminas\\ApiTools\\ContentNegotiation',
        'Laminas\\ApiTools\\ContentValidation',
        'Laminas\\ApiTools\\Rest',
        'Laminas\\ApiTools\\Rpc',
        'Laminas\\ApiTools\\Versioning',
        'LmcCors',
        'AssetManager',

        'Api',

        'DoctrineModule',
        'DoctrineORMModule',
        'Admin',
        'Cluster',
        'Application'
    ],
    'module_listener_options' => [
        'config_glob_paths'        => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'config_cache_enabled'     => !(!defined('PORTAL_ENVIRONMENT')
            || 'development' === PORTAL_ENVIRONMENT),
        'config_cache_key'         => 'ecp-backend',
        'module_map_cache_enabled' => !(!defined('PORTAL_ENVIRONMENT')
            || 'development' === PORTAL_ENVIRONMENT),
        'cache_dir'                => 'data/cache',
        'module_paths'             => [
            './module',
            './vendor',
        ],
    ],
    'service_manager'         => [
        'use_defaults' => true,
        'factories'    => [],
    ],
];
