<?php

declare(strict_types=1);

namespace Admin;

use Cluster\Controller\Cluster\GroupController;
use Cluster\Controller\ProjectController;
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
                    'cluster' => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route' => '/cluster',
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'group' => [
                                'type'          => Literal::class,
                                'options'       => [
                                    'route'    => '/group',
                                    'defaults' => [
                                        'controller' => GroupController::class,
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
                        ],
                    ],
                    'project' => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route'    => '/project',
                            'defaults' => [
                                'controller' => ProjectController::class,
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
                        ],
                    ],
                ],
            ],
        ],
    ],
];
