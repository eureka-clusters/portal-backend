<?php

declare(strict_types=1);

namespace Application;

use Application\Controller\OAuth2Controller;
use Admin\Service\UserService;
use Api\Service\OAuthService;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        OAuth2Controller::class => [
            UserService::class,
            OAuthService::class,
            'Config'
        ],
    ],
];
