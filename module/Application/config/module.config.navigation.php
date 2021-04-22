<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

return [
    'navigation' => [
        // The DefaultNavigationFactory we configured in (1) uses 'default' as the sitemap key
        'default' => [
            'support' => [
                'label'    => _('txt-nav-support'),
                'order'    => 100,
                'route'    => 'support/index',
                'resource' => 'route/support/index',
                'pages'    => [
                    'support'       => [
                        'label' => _('txt-nav-support'),
                        'route' => 'support/index',
                    ],
                    'list-version'  => [
                        'label' => _('txt-nav-support-list-ticket-open'),
                        'route' => 'support/ticket/list/open',
                        'pages' => [
                            'view-version'       => [
                                'label' => _('txt-nav-support-view-moonraker-version'),
                                'route' => 'support/ticket/version/view',
                            ],
                            'list-ticket-open'   => [
                                'label' => _('txt-nav-support-list-ticket-open'),
                                'route' => 'support/ticket/list/open',
                                'pages' => [
                                    'view-ticket' => [
                                        'label' => _('txt-nav-support-view-ticket'),
                                        'route' => 'support/ticket/view',
                                    ],
                                ],
                            ],
                            'list-ticket-closed' => [
                                'label' => _('txt-nav-support-list-ticket-closed'),
                                'route' => 'support/ticket/list/closed',
                            ],
                            'list-ticket-own'    => [
                                'label' => _('txt-nav-support-list-ticket-own'),
                                'route' => 'support/ticket/list/own',
                            ],

                        ],
                    ],
                    'create-ticket' => [
                        'label' => _('txt-nav-support-create-ticket'),
                        'route' => 'support/ticket/create',
                    ],
                ],
            ],
        ],
    ],
];
