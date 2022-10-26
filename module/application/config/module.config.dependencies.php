<?php

declare(strict_types=1);

namespace Application;

use Application\Event\InjectAclInNavigation;
use Application\Event\SetTitle;
use Application\Twig\DatabaseTwigLoader;
use Doctrine\ORM\EntityManager;
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
        DatabaseTwigLoader::class => [
            EntityManager::class,
        ],
    ],
];
