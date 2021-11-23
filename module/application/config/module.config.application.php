<?php

declare(strict_types=1);

use Laminas\Session;

return [
    'service_manager'    => [
        'factories' => [
            // Configures the default SessionManager instance
            Session\ManagerInterface::class       => Session\Service\SessionManagerFactory::class,
            // Provides session configuration to SessionManagerFactory
            Session\Config\ConfigInterface::class => Session\Service\SessionConfigFactory::class,
        ],
    ],
    'session_config'     => [
        'cache_expire'    => 86400,
        'cookie_lifetime' => 31536000,
        'name'            => 'portal-backend',
    ],
];
