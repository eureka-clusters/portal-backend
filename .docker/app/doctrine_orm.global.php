<?php use Doctrine\DBAL\Driver\PDO\MySQL\Driver;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => Driver::class,
                'params'      => [
                    'host'          => 'db_host',
                    'port'          => '3306',
                    'user'          => 'db_user',
                    'password'      => 'db_user',
                    'dbname'        => 'db_name',
                    'driverOptions' => [
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                    ]
                ],
            ],
        ],
    ],
];
