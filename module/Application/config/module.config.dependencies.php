<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Application;

use Admin\Service\UserService;
use Api\Service\OAuthService;
use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Controller\OAuth2Controller::class              => [
            UserService::class,
            OAuthService::class,
            'Config'
        ],
        Authentication\OAuth2\Adapter\PdoAdapter::class => [
            EntityManager::class
        ],
    ]
];
