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
        'storage'                        => \Application\Authentication\OAuth2\Adapter\PdoAdapter::class,
        'allow_implicit'                 => true,
        'access_lifetime'                => 100,
        'enforce_state'                  => true,
        'options' => [
            'use_jwt_access_tokens'             => false,
            'store_encrypted_token_string'      => true,
            'use_openid_connect'                => false,
            'id_lifetime'                       => 100,
            'www_realm'                         => 'Service',
            'token_param_name'                  => 'access_token',
            'token_bearer_header_name'          => 'Bearer',
            'require_exact_redirect_uri'        => true,
            'allow_credentials_in_request_body' => true,
            'allow_public_clients'              => true,
            'always_issue_new_refresh_token'    => true,
            'unset_refresh_token_after_use'     => true,
        ],
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
                        'storage' => \Application\Authentication\OAuth2\Adapter\PdoAdapter::class
                    ]
                ],
            ],
        ],
    ],
];
