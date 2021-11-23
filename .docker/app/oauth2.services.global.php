<?php

return [
    'api_options'     => [
        'cryptoKey' => 'my_crypto_key'
    ],
    'oauth2-settings' => [
        'services' => [
            'itea'   => [
                'name'       => 'ITEA Office',
                'settings'   => [
                    'clientId'                => '__itea_client_id__',
                    'clientSecret'            => '__itea_client_secret__',
                    'redirectUri'             => 'https://portal.backend.docker.localhost/oauth2/callback.html',
                    'urlAuthorize'            => 'https://iteaoffice.docker.localhost/oauth/authorize',
                    'urlAccessToken'          => 'https://iteaoffice.docker.localhost',
                    'allowedClusters'         => ['itea'],
                    'urlResourceOwnerDetails' => 'itea_office',
                    'scopes'                  => 'User.Read'
                ],
                'profileUrl' => 'https://iteaoffice.docker.localhost/api/me',
            ],
            'celtic' => [
                'name'       => 'Celtic Office',
                'settings'   => [
                    'clientId'                => '__celtic_client_id__',
                    'clientSecret'            => '__celtic_client_id__',
                    'redirectUri'             => 'https://portal.backend.docker.localhost/oauth2/callback.html',
                    'urlAuthorize'            => 'https://cluster-projects.eurestools.eu/oauth2/authorize',
                    'urlAccessToken'          => 'https://cluster-projects.eurestools.eu/oauth2/token',
                    'urlResourceOwnerDetails' => 'itea_office',
                    'allowedClusters'         => ['celtic'],
                    'scopes'                  => 'User.Read'
                ],
                'profileUrl' => 'https://cluster-projects.eurestools.eu/api/me',
            ],
        ],
    ]
];
