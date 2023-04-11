<?php

declare(strict_types=1);

namespace Admin;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Reporting\Controller\DownloadController;
use Reporting\Controller\ReportingController;
use Reporting\Controller\StorageLocationController;

return [
    'router' => [
        'routes' => [
            'zfcadmin' => [
                'child_routes' => [
                    'reporting' => [
                        'type'          => Literal::class,
                        'options'       => [
                            'route'    => '/reporting',
                            'defaults' => [
                                'controller' => ReportingController::class,
                                'action'     => 'list',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes'  => [
                            'index'            => [
                                'type'    => Literal::class,
                                'options' => [
                                    'route'    => '/index.html',
                                    'defaults' => [
                                        'action' => 'index',
                                    ],
                                ],
                            ],
                            'download'         => [
                                'type'          => Literal::class,
                                'options'       => [
                                    'route'    => '/download',
                                    'defaults' => [
                                        'controller' => DownloadController::class,
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'blob' => [
                                        'type'    => Segment::class,
                                        'options' => [
                                            'route'    => '/blob/[:name]',
                                            'defaults' => [
                                                'action' => 'blob',
                                            ],
                                        ],
                                    ],
                                ]
                            ],
                            'storage-location' => [
                                'type'          => Segment::class,
                                'options'       => [
                                    'route'    => '/storage-location',
                                    'defaults' => [
                                        'controller' => StorageLocationController::class,
                                        'action'     => 'list',
                                        'page'       => 1,
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
                                    'new'  => [
                                        'type'    => Literal::class,
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
