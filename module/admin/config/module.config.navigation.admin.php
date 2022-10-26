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
            'user' => [
                'label' => _('txt-user-list'),
                'route' => 'zfcadmin/user/list',
                'resource' => 'route/zfcadmin/user/list',
                'privilege' => 'list',
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
                'resource' => 'route/zfcadmin/role/list',
                'privilege' => 'list',
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
            'oauth2-scopes' => [
                'label' => _('txt-oauth2-scopes-list'),
                'route' => 'zfcadmin/oauth2/scope/list',
                'resource' => 'route/zfcadmin/oauth2/scope/list',
                'privilege' => 'list',
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
                'resource' => 'route/zfcadmin/oauth2/client/list',
                'privilege' => 'list',
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
            'new' => [
                'label' => _("txt-cache-management"),
                'route' => 'zfcadmin/cache/index',
                'resource' => 'route/zfcadmin/cache/index',
                'privilege' => 'index',
            ],
        ],
    ],
];
