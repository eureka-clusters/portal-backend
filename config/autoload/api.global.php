<?php

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
        'storage'         => \Laminas\ApiTools\OAuth2\Adapter\PdoAdapter::class,
        'options'         => [
            'always_issue_new_refresh_token' => true,
            'use_jwt_access_tokens'          => false, //If set to TRUE we have only JWT tokens, if set to FALSE we have the regular tokens
            'jwt_extra_payload_callable'     => null,
            'auth_code_lifetime'             => 3000,
            'issuer'                         => 'eureka-clusters',
            'use_openid_connect'             => true,
        ],
        'allow_implicit'  => true,
        'access_lifetime' => 36000,
        'enforce_state'   => true,
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
                        'storage' => \Laminas\ApiTools\OAuth2\Adapter\PdoAdapter::class
                    ]
                ],
            ],
        ],
    ],
];
