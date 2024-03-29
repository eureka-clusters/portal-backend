<?php

return [
    'modules'                 => [
        'Laminas\\Router',
        'Laminas\\Form',
        'Laminas\\InputFilter',
        'Laminas\\Navigation',
        'Laminas\\Hydrator',
        'Laminas\\Paginator',
        'Laminas\\Cache',

        'Laminas\\Mvc\\Plugin\\FlashMessenger',
        'Laminas\\Mvc\\Plugin\\Identity',

        'Laminas\\ApiTools',
        'Laminas\\ApiTools\\ApiProblem',
        'Laminas\\ApiTools\\OAuth2',
        'Laminas\\ApiTools\\MvcAuth',
        'Laminas\\ApiTools\\Hal',
        'Laminas\\ApiTools\\ContentNegotiation',
        'Laminas\\ApiTools\\ContentValidation',
        'Laminas\\ApiTools\\Rest',
        'Laminas\\ApiTools\\Rpc',
        'Laminas\\ApiTools\\Versioning',

        'AssetManager',
        'LaminasBootstrap5',

        'ZfcTwig',
        'BjyAuthorize',
        'Jield\\Authorize',

        'LmcCors',

        'Api',

        'DoctrineModule',
        'DoctrineORMModule',
        'Admin',
        'Cluster',
        'Application',
        'Reporting',
        'Deeplink',
        'Mailing',

    ],
    'module_listener_options' => [
        'config_glob_paths'        => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'config_cache_enabled'     => !(!defined(constant_name: 'PORTAL_ENVIRONMENT')
            || ('development' === PORTAL_ENVIRONMENT)),
        'config_cache_key'         => 'ecp-backend',
        'module_map_cache_enabled' => !(!defined(constant_name: 'PORTAL_ENVIRONMENT')
            || ('development' === PORTAL_ENVIRONMENT)),
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
