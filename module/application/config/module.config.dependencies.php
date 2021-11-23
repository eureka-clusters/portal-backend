<?php

declare(strict_types=1);

namespace Application;

use Admin\Service\UserService;
use Api\Options\ModuleOptions;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Controller\OAuth2Controller::class => [
            UserService::class,
            ModuleOptions::class
        ],
    ],
];
