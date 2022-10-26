<?php

declare(strict_types=1);

namespace Application;

use Application\Event\InjectAclInNavigation;
use Application\Event\SetTitle;
use Jield\Authorize\Service\AuthorizeService;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        InjectAclInNavigation::class => [
            AuthorizeService::class,
            'Config',
        ],
        SetTitle::class => [
            'ViewRenderer'
        ],
    ],
];
