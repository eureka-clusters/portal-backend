<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Application\Authentication\Factory;

use Application\Authentication\OAuth2\Adapter\PdoAdapter;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;

final class PdoAdapterFactory
{
    public function __invoke(ContainerInterface $container): PdoAdapter
    {
        $config = $container->get('config');

        $oauthConfig = $config['api-tools-oauth2'];

        $oauth2ServerConfig = [];
        if (isset($oauthConfig['storage_settings']) && is_array($oauthConfig['storage_settings'])) {
            $oauth2ServerConfig = $oauthConfig['storage_settings'];
        }

        return new PdoAdapter(
            $container->get(EntityManager::class),
            $oauth2ServerConfig
        );
    }
}
