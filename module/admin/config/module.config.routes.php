<?php

declare(strict_types=1);

namespace Admin;

use Admin\Controller\UserController;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'user' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/user',
                    'defaults' => [
                        'controller' => UserController::class,
                        'action' => 'list',
                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'login' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/login.html',
                            'defaults' => [
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/logout.html',
                            'defaults' => [
                                'action' => 'logout',
                            ],
                        ],
                    ],
                    'change-password' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/change-password.html',
                            'defaults' => [
                                'action' => 'change-password',
                            ],
                        ],
                    ],
                    'lost-password' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/lost-password.html',
                            'defaults' => [
                                'action' => 'lost-password',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
