<?php

declare(strict_types=1);

namespace Admin\Service;

use Admin\Entity\Role;
use Application\Service\AbstractService;

class AdminService extends AbstractService
{
    public function canDeleteRole(Role $role): bool
    {
        $cannotDeleteRole = [];

        if ($role->isLocked()) {
            $cannotDeleteRole[] = 'This role is locked';
        }

        if (!$role->getUsers()->isEmpty()) {
            $cannotDeleteRole[] = 'This role has users';
        }

        return count($cannotDeleteRole) === 0;
    }
}
