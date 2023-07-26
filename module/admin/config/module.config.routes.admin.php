<?php

declare(strict_types=1);

namespace Admin;

use Admin\Controller;
use Admin\Controller\CacheController;
use Admin\Controller\OAuth2\ClientController;
use Admin\Controller\OAuth2\ScopeController;
use Admin\Controller\RoleController;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'zfcadmin' => [
                'type'          => Literal::class,
                'options'       => [
                    'route' => '/admin',
                ],
                'may_terminate' => false,
                'child_routes'  => [
                    'index'  => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/index.html',
                            'defaults' => [
                                'controller' => Controller\AdminController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'user'   => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route'    => '/user',
                            'defaults' => [
                                'controller' => Controller\UserController::class,
                                'action'     => 'list',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'list'           => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'view'           => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'edit'           => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                            'generate-token' => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'    => '/generate-token/[:id].html',
                                    'defaults' => [
                                        'action' => 'generate-token',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'role'   => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route'    => '/role',
                            'defaults' => [
                                'controller' => RoleController::class,
                                'action'     => 'list',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'list' => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                    'defaults' => [
                                        'action' => 'list',
                                    ],
                                ],
                            ],
                            'view' => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'    => '/view/[:id].html',
                                    'defaults' => [
                                        'action' => 'view',
                                    ],
                                ],
                            ],
                            'new'  => [
                                'type'    => Literal::class,
                                'options' => [
                                    'route'    => '/new.html',
                                    'defaults' => [
                                        'action' => 'new',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type'    => Segment::class,
                                'options' => [
                                    'route'    => '/edit/[:id].html',
                                    'defaults' => [
                                        'action' => 'edit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'cache'  => [
                        'type'          => Segment::class,
                        'options'       => [
                            'route'    => '/cache',
                            'defaults' => [
                                'controller' => CacheController::class,
                                'action'     => 'index',
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'index' => [
                                'type'    => Literal::class,
                                'options' => [
                                    'route'    => '/index.html',
                                    'defaults' => [
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'oauth2' => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route' => '/oauth2',
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'scope'   => [
                                'type'          => Segment::class,
                                'options'       => [
                                    'route'    => '/scope',
                                    'defaults' => [
                                        'controller' => ScopeController::class,
                                        'action'     => 'list',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes'  => [
                                    'list' => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                    ],
                                    'view' => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                    'new'  => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/new.html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'edit' => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'client'  => [
                                'type'          => Segment::class,
                                'options'       => [
                                    'route'    => '/client',
                                    'defaults' => [
                                        'controller' => ClientController::class,
                                        'action'     => 'list',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes'  => [
                                    'list' => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                    ],
                                    'view' => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                    'new'  => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/new.html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'edit' => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'service' => [
                                'type'          => Segment::class,
                                'options'       => [
                                    'route'    => '/service',
                                    'defaults' => [
                                        'controller' => Controller\OAuth2\ServiceController::class,
                                        'action'     => 'list',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes'  => [
                                    'list' => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/list[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                    ],
                                    'view' => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                    'new'  => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/new.html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'edit' => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
