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

use Admin\Entity\Permit\Entity as EntityEntity;
use Admin\Entity\User as UserEntity;
use Doctrine\ORM\EntityRepository;

use function sprintf;

class Entity extends EntityRepository
{
    public function flushPermitsByEntity(EntityEntity $entity): void
    {
        $this->removePermitsForEntity($entity);

        /**
         * Go over each setter and role to add the rules in the database for the entity
         */
        foreach ($entity->getSetter() as $setter) {
            foreach ($setter->getRole() as $role) {
                $insertQuery = sprintf(
                    'INSERT INTO admin_permit_user (role_id, user_id, key_id, setter_id) SELECT %s, %s, %s, %s FROM %s',
                    $role->getId(),
                    $setter->getUserKey(),
                    $setter->getEntityKey(),
                    $setter->getId(),
                    $setter->getFromClause()
                );
                if (!empty($setter->getWhereClause())) {
                    $insertQuery .= sprintf(' WHERE %s ', $setter->getWhereClause());
                }

                $insertQuery .= sprintf(
                    ' ON DUPLICATE KEY UPDATE role_id = %s, user_id = %s,  key_id = %s',
                    $role->getId(),
                    $setter->getUserKey(),
                    $setter->getEntityKey()
                );

                $this->_em->getConnection()->executeStatement($insertQuery);
            }
        }
    }

    public function removePermitsForEntity(EntityEntity $entity): void
    {
        //Delete first all entries in the admin_permit_user connected to the given entity
        //This can be done by checking the roles connected to the entity itself
        $deleteQuery = sprintf(
            'DELETE FROM admin_permit_user WHERE role_id IN (SELECT id FROM admin_permit_role WHERE entity_id = %d)',
            $entity->getId()
        );
        $this->_em->getConnection()->executeStatement($deleteQuery);
    }

    public function flushPermitsByEntityAndId(EntityEntity $entity, $id): void
    {
        /**
         * Delete first the roles which have no setter at all (and will no be touched later
         */
        foreach ($entity->getRole() as $role) {
            if ($role->getSetter()->count() === 0) {
                $deleteQuery = sprintf(
                    'DELETE FROM admin_permit_user WHERE role_id = %d AND key_id = %s',
                    $role->getId(),
                    $id
                );
                $this->_em->getConnection()->executeStatement($deleteQuery);
            }
        }
        /**
         * Throw first the permit_users away which have the role and key_id
         */
        foreach ($entity->getSetter() as $setter) {
            foreach ($setter->getRole() as $role) {
                $deleteQuery = sprintf(
                    'DELETE FROM admin_permit_user WHERE role_id = %d AND key_id = %s',
                    $role->getId(),
                    $id
                );
                $this->_em->getConnection()->executeStatement($deleteQuery);
            }
        }
        /**
         * Build the table again
         */
        foreach ($entity->getSetter() as $setter) {
            /**
             * Now insert the roles again
             */
            foreach ($setter->getRole() as $role) {
                $insertQuery = sprintf(
                    'INSERT INTO admin_permit_user (role_id, user_id, key_id, setter_id) SELECT %s, %s, %s, %s FROM %s ',
                    $role->getId(),
                    $setter->getUserKey(),
                    $setter->getEntityKey(),
                    $setter->getId(),
                    $setter->getFromClause()
                );
                if (!empty($setter->getWhereClause())) {
                    $insertQuery .= sprintf(' WHERE %s ', $setter->getWhereClause());
                }
                if (stripos($insertQuery, 'where') !== false) {
                    $insertQuery .= sprintf(' AND %s = %s', $setter->getEntityKey(), $id);
                } else {
                    $insertQuery .= sprintf(' WHERE %s = %s', $setter->getEntityKey(), $id);
                }

                $insertQuery .= sprintf(
                    ' ON DUPLICATE KEY UPDATE role_id = %s, user_id = %s,  key_id = %s',
                    $role->getId(),
                    $setter->getUserKey(),
                    $setter->getEntityKey()
                );

                $this->_em->getConnection()->executeStatement($insertQuery);
            }
        }
    }

    public function flushPermitsByUser(UserEntity $user): void
    {
        /**
         * Throw first the permit_users away which have the role and key_id
         */
        $deleteQuery = sprintf('DELETE FROM admin_permit_user WHERE user_id = %d', $user->getId());
        $this->_em->getConnection()->executeStatement($deleteQuery);


        //Now go over all entities and flush the privileges for the user
        /**
         * @var $entities EntityEntity[]
         */
        $entities = $this->_em->getRepository(EntityEntity::class)->findAll();
        /**
         * Build the table again
         */
        foreach ($entities as $entity) {
            foreach ($entity->getSetter() as $setter) {
                /**
                 * Now insert the roles again
                 */
                foreach ($setter->getRole() as $role) {
                    $insertQuery = sprintf(
                        'INSERT INTO admin_permit_user (role_id, user_id, key_id, setter_id) SELECT %s, %s, %s, %s FROM %s',
                        $role->getId(),
                        $setter->getUserKey(),
                        $setter->getEntityKey(),
                        $setter->getId(),
                        $setter->getFromClause()
                    );
                    if (!empty($setter->getWhereClause())) {
                        $insertQuery .= sprintf(' WHERE %s ', $setter->getWhereClause());
                    }
                    if (stripos($insertQuery, 'where') !== false) {
                        $insertQuery .= sprintf(' AND %s = %s', $setter->getUserKey(), $user->getId());
                    } else {
                        $insertQuery .= sprintf(' WHERE %s = %s', $setter->getUserKey(), $user->getId());
                    }

                    $insertQuery .= sprintf(
                        ' ON DUPLICATE KEY UPDATE role_id = %s, user_id = %s,  key_id = %s',
                        $role->getId(),
                        $setter->getUserKey(),
                        $setter->getEntityKey()
                    );

                    $this->_em->getConnection()->executeStatement($insertQuery);
                }
            }
        }
    }
}
