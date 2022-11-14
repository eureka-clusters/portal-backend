<?php

declare(strict_types=1);

namespace Deeplink;

use Deeplink\Controller\DeeplinkController;
use Deeplink\Controller\TargetController;
use Laminas\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'deeplink' => [
                'type'     => Segment::class,
                'priority' => -1000,
                'options'  => [
                    'route'       => '/d/[:hash]',
                    'constraints' => [
                        'id' => '\d+',
                    ],
                    'defaults'    => [
                        'controller' => DeeplinkController::class,
                        'action'     => 'deeplink',
                    ],
                ],
            ],
            'zfcadmin' => [
                'child_routes' => [
                    'deeplink' => [
                        'type'          => Segment::class,
                        'priority'      => 1000,
                        'options'       => [
                            'route' => '/deeplink',
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'target' => [
                                'type'          => Segment::class,
                                'priority'      => 1000,
                                'options'       => [
                                    'route'    => '/target',
                                    'defaults' => [
                                        'controller' => TargetController::class,
                                        'action'     => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes'  => [
                                    'list' => [
                                        'type'     => Segment::class,
                                        'priority' => 1000,
                                        'options'  => [
                                            'route'    => '/list.html',
                                            'defaults' => [
                                                'action' => 'list',
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
                                    'view' => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'view',
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
