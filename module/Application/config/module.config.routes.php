<?php
/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Admin;

use Application\Controller;

return [
    'router' => [
        'routes' => [
            'home'   => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'oauth2' => [
                'type'          => 'Literal',
                'options'       => [
                    'route'    => '/oauth2',
                    'priority' => 1000,
                    'defaults' => [
                        'controller' => Controller\OAuth2Controller::class,
                        'action'     => 'list',
                    ],
                ],
                'may_terminate' => false,
                'child_routes'  => [
                    'login'    => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/login/via/[:service].html',
                            'defaults' => [
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'callback' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/callback.html',
                            'defaults' => [
                                'action' => 'callback',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
