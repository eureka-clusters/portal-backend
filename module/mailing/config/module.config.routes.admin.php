<?php

declare(strict_types=1);

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Mailing\Controller\EmailController;
use Mailing\Controller\MailerController;
use Mailing\Controller\SenderController;
use Mailing\Controller\TemplateController;
use Mailing\Controller\TransactionalController;

return [
    'router' => [
        'routes' => [
            'zfcadmin' => [
                'child_routes' => [
                    'mailing' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/mailing',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'sender' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/sender',
                                    'defaults' => [
                                        'controller' => SenderController::class,
                                        'action' => 'list',
                                        'page' => 1,
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'list' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/list[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                        'query' => [
                                            'search' => null,
                                            'page' => null,
                                        ],
                                    ],
                                    'new' => [
                                        'type' => Literal::class,
                                        'options' => [
                                            'route' => '/new.html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'view' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                    'edit' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'mailer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/mailer',
                                    'defaults' => [
                                        'controller' => MailerController::class,
                                        'action' => 'list',
                                        'page' => 1,
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'list' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/list[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                        'query' => [
                                            'search' => null,
                                            'page' => null,
                                        ],
                                    ],
                                    'new' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/new/service-[:service].html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'view' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                    'edit' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'template' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/template',
                                    'defaults' => [
                                        'controller' => TemplateController::class,
                                        'action' => 'list',
                                        'page' => 1,
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'list' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/list.html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                        'query' => [
                                            'search' => null,
                                            'page' => null,
                                        ],
                                    ],
                                    'new' => [
                                        'type' => Literal::class,
                                        'options' => [
                                            'route' => '/new.html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'view' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                    'edit' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'transactional' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/transactional',
                                    'defaults' => [
                                        'controller' => TransactionalController::class,
                                        'action' => 'list',
                                        'page' => 1,
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'list' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/list[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                        'query' => [
                                            'search' => null,
                                            'page' => null,
                                        ],
                                    ],
                                    'new' => [
                                        'type' => Literal::class,
                                        'options' => [
                                            'route' => '/new.html',
                                            'defaults' => [
                                                'action' => 'new',
                                            ],
                                        ],
                                    ],
                                    'view' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/view/[:id].html',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                        ],
                                    ],
                                    'edit' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/edit/[:id].html',
                                            'defaults' => [
                                                'action' => 'edit',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'email' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/email',
                                    'defaults' => [
                                        'controller' => EmailController::class,
                                        'action' => 'list',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes' => [
                                    'list' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/list[/f-:encodedFilter][/page-:page].html',
                                            'defaults' => [
                                                'action' => 'list',
                                            ],
                                        ],
                                    ],
                                    'view' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/view/[:id].html',
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
        ],
    ],
];
