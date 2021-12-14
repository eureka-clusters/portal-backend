<?php

declare(strict_types=1);

namespace Admin;

use Application\Controller\IndexController;
use Application\Controller\OAuth2Controller;
use Application\Controller;

return [
    'router' => [
        'routes' => [
            'home'   => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'oauth2' => [
                'type'          => 'Literal',
                'options'       => [
                    'route'    => '/oauth2',
                    'priority' => 1000,
                    'defaults' => [
                        'controller' => OAuth2Controller::class,
                        'action'     => 'list',
                    ],
                ],
                'may_terminate' => false,
                'child_routes'  => [
                    'login'    => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/login/via/[:service].html',
                            'defaults' => [
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'callback' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/callback.html',
                            'defaults' => [
                                'action' => 'callback',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
