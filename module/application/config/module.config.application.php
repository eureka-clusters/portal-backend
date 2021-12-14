<?php

declare(strict_types=1);
use Laminas\Session\ManagerInterface;
use Laminas\Session\Service\SessionManagerFactory;
use Laminas\Session\Config\ConfigInterface;
use Laminas\Session\Service\SessionConfigFactory;

use Laminas\Session;

return [
    'service_manager'    => [
        'factories' => [
            // Configures the default SessionManager instance
            ManagerInterface::class       => SessionManagerFactory::class,
            // Provides session configuration to SessionManagerFactory
            ConfigInterface::class => SessionConfigFactory::class,
        ],
    ],
    'session_config'     => [
        'cache_expire'    => 86400,
        'cookie_lifetime' => 31_536_000,
        'name'            => 'portal-backend',
    ],
];
