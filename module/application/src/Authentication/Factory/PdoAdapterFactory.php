<?php

declare(strict_types=1);

namespace Application\Authentication\Factory;

use Application\Authentication\Adapter\PdoAdapter;
use Interop\Container\ContainerInterface;

use function is_array;
use function sprintf;

final class PdoAdapterFactory
{
    public function __invoke(ContainerInterface $container): PdoAdapter
    {
        $config = $container->get('config');

        $oauthConfig = $config['api-tools-oauth2'];

        //Grab the params directly from the Doctrine Params
        $username = $config['doctrine']['connection']['orm_default']['params']['user'] ?? null;
        $password = $config['doctrine']['connection']['orm_default']['params']['password'] ?? null;
        $options  = $config['doctrine']['connection']['orm_default']['params']['driverOptions'] ?? [];

        $dsn = sprintf(
            'mysql:dbname=%s;host=%s',
            $config['doctrine']['connection']['orm_default']['params']['dbname'],
            $config['doctrine']['connection']['orm_default']['params']['host']
        );

        $oauth2ServerConfig = [];
        if (isset($oauthConfig['storage_settings']) && is_array($oauthConfig['storage_settings'])) {
            $oauth2ServerConfig = $oauthConfig['storage_settings'];
        }

        //Add 2 own options
        $oauth2ServerConfig['bcrypt_cost'] = 14;
        $oauth2ServerConfig['user_table']  = 'admin_user';

        return new PdoAdapter(
            [
                'dsn'      => $dsn,
                'username' => $username,
                'password' => $password,
                'options'  => $options,
            ],
            $oauth2ServerConfig
        );
    }
}
