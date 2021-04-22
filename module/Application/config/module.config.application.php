<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

use Laminas\Session;
use OcraCachedViewResolver\Module;

return [
    'service_manager' => [
        'factories' => [
            // Configures the default SessionManager instance
            Session\ManagerInterface::class       => Session\Service\SessionManagerFactory::class,
            // Provides session configuration to SessionManagerFactory
            Session\Config\ConfigInterface::class => Session\Service\SessionConfigFactory::class,
        ],
    ],
    'session_config'  => [
        'cache_expire'    => 86400,
        'cookie_lifetime' => 31536000,
        'name'            => 'portal-backend',
    ],
];
