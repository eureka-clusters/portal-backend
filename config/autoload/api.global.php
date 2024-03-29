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
        'storage'         => \Application\Authentication\Adapter\PdoAdapter::class,
        'options'         => [
            'always_issue_new_refresh_token' => true,
            'use_jwt_access_tokens'          => true,
            'jwt_extra_payload_callable'     => null,
            'auth_code_lifetime'             => 3600,
            'issuer'                         => 'eureka-clusters',
            'use_openid_connect'             => true,
        ],
        'allow_implicit'  => true,
        'access_lifetime' => 3600,
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
                        'storage' => \Application\Authentication\Adapter\PdoAdapter::class
                    ]
                ],
            ],
        ],
    ],
];
