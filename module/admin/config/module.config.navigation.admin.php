<?php

declare(strict_types=1);

use Admin\Entity\Role;
use Admin\Entity\User;
use Admin\Navigation\Invokable\OAuth2\ClientLabel;
use Admin\Navigation\Invokable\OAuth2\ScopeLabel;
use Admin\Navigation\Invokable\RoleLabel;
use Admin\Navigation\Invokable\UserLabel;
use Api\Entity\OAuth\Client;
use Api\Entity\OAuth\Scope;

return [
    'navigation' => [
        'default' => [
            'uses-and-roles' => [
                'label' => _('txt-user-and-roles'),
                'resource' => 'route/zfcadmin/user/list',
                'privilege' => 'list',
                'uri' => '#',
                'pages' => [
                    'user' => [
                        'label' => _('txt-user-list'),
                        'route' => 'zfcadmin/user/list',
                        'pages' => [
                            'view' => [
                                'route' => 'zfcadmin/user/view',
                                'label' => _('txt-view-user'),
                                'params' => [
                                    'entities' => [
                                        'id' => User::class,
                                    ],
                                    'invokables' => [
                                        UserLabel::class,
                                    ],
                                ],

                            ],
                        ],
                    ],
                    'role' => [
                        'label' => _('txt-role-list'),
                        'route' => 'zfcadmin/role/list',
                        'pages' => [
                            'view' => [
                                'label' => 'test',
                                'route' => 'zfcadmin/role/view',
                                'params' => [
                                    'entities' => [
                                        'id' => Role::class,
                                    ],
                                    'invokables' => [
                                        RoleLabel::class,
                                    ],
                                ],
                                'pages' => [
                                    'edit' => [
                                        'label' => _('txt-edit-role'),
                                        'route' => 'zfcadmin/role/edit',
                                        'params' => [
                                            'entities' => [
                                                'id' => Role::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new' => [
                                'label' => _('txt-add-role'),
                                'route' => 'zfcadmin/role/new',
                            ],
                        ],
                    ],
                ]
            ],
            'oauth2' => [
                'label' => _('txt-oauth2'),
                'resource' => 'route/zfcadmin/oauth2/scope/list',
                'privilege' => 'list',
                'uri' => '#',
                'pages' => [
                    'oauth2-scopes' => [
                        'label' => _('txt-oauth2-scopes-list'),
                        'route' => 'zfcadmin/oauth2/scope/list',
                        'pages' => [
                            'view' => [
                                'label' => _('txt-oauth2-scope'),
                                'route' => 'zfcadmin/oauth2/scope/view',
                                'params' => [
                                    'entities' => [
                                        'id' => Scope::class,
                                    ],
                                    'invokables' => [
                                        ScopeLabel::class,
                                    ],
                                ],
                                'pages' => [
                                    'edit' => [
                                        'label' => _('txt-edit-oauth2-scope'),
                                        'route' => 'zfcadmin/oauth2/scope/edit',
                                        'params' => [
                                            'entities' => [
                                                'id' => Scope::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new' => [
                                'label' => _("txt-new-oauth2-scopes"),
                                'route' => 'zfcadmin/oauth2/scope/new',
                            ],
                        ],
                    ],
                    'oauth2-clients' => [
                        'label' => _('txt-oauth2-clients-list'),
                        'route' => 'zfcadmin/oauth2/client/list',
                        'pages' => [
                            'view' => [
                                'label' => _('txt-oauth2-client'),
                                'route' => 'zfcadmin/oauth2/client/view',
                                'params' => [
                                    'entities' => [
                                        'id' => Client::class,
                                    ],
                                    'property' => 'clientId',
                                    'invokables' => [
                                        ClientLabel::class,
                                    ],
                                ],
                                'pages' => [
                                    'edit' => [
                                        'label' => _('txt-edit-client'),
                                        'route' => 'zfcadmin/oauth2/client/edit',
                                        'params' => [
                                            'entities' => [
                                                'id' => Client::class,
                                            ],
                                            'property' => 'clientId',
                                            'invokables' => [
                                                ClientLabel::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new' => [
                                'label' => _("txt-new-oauth2-clients"),
                                'route' => 'zfcadmin/oauth2/client/new',
                            ],
                        ],
                    ],
                    'oauth2-services' => [
                        'label' => _('txt-oauth2-service-list'),
                        'route' => 'zfcadmin/oauth2/service/list',
                        'pages' => [
                            'view' => [
                                'label' => _('txt-oauth2-service'),
                                'route' => 'zfcadmin/oauth2/service/view',
                                'params' => [
                                    'entities' => [
                                        'id' => \Api\Entity\OAuth\Service::class,
                                    ],
                                    'invokables' => [
                                        \Admin\Navigation\Invokable\OAuth2\ServiceLabel::class,
                                    ],
                                ],
                                'pages' => [
                                    'edit' => [
                                        'label' => _('txt-edit-service'),
                                        'route' => 'zfcadmin/oauth2/service/edit',
                                        'params' => [
                                            'entities' => [
                                                'id' => \Api\Entity\OAuth\Service::class,
                                            ],
                                            'invokables' => [
                                                \Admin\Navigation\Invokable\OAuth2\ServiceLabel::class,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'new' => [
                                'label' => _("txt-new-oauth2-services"),
                                'route' => 'zfcadmin/oauth2/service/new',
                            ],
                        ],
                    ],
                ],

            ],

            'cache' => [
                'label' => _("txt-cache-management"),
                'route' => 'zfcadmin/cache/index',
                'resource' => 'route/zfcadmin/cache/index',
                'privilege' => 'index',
            ],
        ],
    ],
];
