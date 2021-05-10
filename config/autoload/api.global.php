<?php
/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

use Laminas\ApiTools\MvcAuth\Authentication\OAuth2Adapter;

return [
    'router'             => [
        'routes' => [
            'oauth' => [
                'options' => [
                    'spec'  => '%oauth%',
                    'regex' => '(?P<oauth>(/oauth))',
                ],
                'type'    => 'regex',
            ],
        ],
    ],
    'api-tools-oauth2'   => [
        'storage'                        => 'paportal_pdo_adapter',
        'always_issue_new_refresh_token' => true,
        'allow_implicit'                 => true,
        'access_lifetime'                => 3600,
        'enforce_state'                  => true,
    ],
    'api-tools-mvc-auth' => [
        'authentication' => [
            'map'      => [
                'Api\\V1' => 'oauth2_pdo',
            ],
            'adapters' => [
                'oauth2_pdo' => [
                    'adapter' => OAuth2Adapter::class,
                    'storage' => [
                        'storage' => 'paportal_pdo_adapter'
                    ]
                ],
            ],
        ],
    ],
];
