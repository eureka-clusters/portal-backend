<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Admin\Repository\Permit;

use Admin\Entity\Permit\Role as RoleEntity;
use Admin\Entity\User as UserEntity;
use Doctrine\ORM\EntityRepository;

use function sprintf;

final class Role extends EntityRepository
{
    public function insertPermitsForRoleByUser(RoleEntity $role, UserEntity $user, \Admin\Entity\Role $accessRole): void
    {
        $insertQuery = sprintf(
            'INSERT INTO admin_permit_user (role_id, user_id, key_id, access_role_id) SELECT %s, %s, %s, %s FROM %s',
            $role->getId(),
            $user->getId(),
            'id',
            $accessRole->getId(),
            $role->getEntity()->getDatabaseTableName()
        );

        $insertQuery .= sprintf(
            ' ON DUPLICATE KEY UPDATE role_id = %s, user_id = %s, key_id = %s',
            $role->getId(),
            $user->getId(),
            'id'
        );

        $this->_em->getConnection()->executeStatement($insertQuery);
    }

    public function insertPermitsForRoleByUserAndId(
        RoleEntity $role,
        UserEntity $user,
        $id,
        \Admin\Entity\Role $accessRole
    ): void {
        $insertQuery = sprintf(
            'INSERT INTO admin_permit_user (role_id, user_id, key_id, access_role_id) VALUES (%s, %s, %s, %s)',
            $role->getId(),
            $user->getId(),
            $id,
            $accessRole->getId()
        );

        $insertQuery .= sprintf(
            ' ON DUPLICATE KEY UPDATE role_id = %s, user_id = %s,  key_id = %s',
            $role->getId(),
            $user->getId(),
            $id
        );

        $this->_em->getConnection()->executeStatement($insertQuery);
    }
}
