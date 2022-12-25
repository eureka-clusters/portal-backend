<?php

declare(strict_types=1);

namespace Admin;

use Application\Controller\IndexController;
use Application\Controller\OAuth2Controller;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'home'   => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'oauth2' => [
                'type'          => Literal::class,
                'options'       => [
                    'route'    => '/oauth2',
                    'defaults' => [
                        'controller' => OAuth2Controller::class,
                        'action'     => 'list',
                    ],
                ],
                'may_terminate' => false,
                'child_routes'  => [
                    'login'    => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/login/via/[:id]/[:name].html',
                            'defaults' => [
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'callback' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/callback.html',
                            'defaults' => [
                                'action' => 'callback',
                            ],
                        ],
                    ],
                    'refresh'  => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/refresh.html',
                            'defaults' => [
                                'action' => 'refresh',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
