<?php

namespace BjyAuthorize;

use Admin\Entity\Role;
use Admin\Service\UserService;

return [
    'jield_authorize' => [
        'default_role'       => Role::ROLE_PUBLIC,
        'authenticated_role' => Role::ROLE_USER,
        'access_service'     => UserService::class,
        'permit_service'     => UserService::class,
        'cache_enabled'      => true,
        'role_entity_class'  => Role::class,
    ],
];