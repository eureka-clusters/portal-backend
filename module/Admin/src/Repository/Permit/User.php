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

use Admin\Entity;
use Doctrine\ORM\EntityRepository;

/**
 * Class User
 *
 * @package Admin\Repository\Permit
 */
final class User extends EntityRepository
{
    public function userHasPermit(Entity\User $user, string $role, string $entity, int $keyId): bool
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_permit_user');
        $qb->from(Entity\Permit\User::class, 'admin_entity_permit_user');
        $qb->andWhere('admin_entity_permit_user.user = ?1');
        $qb->setParameter(1, $user);
        $qb->andWhere('admin_entity_permit_user.keyId = ?2');
        $qb->setParameter(2, $keyId);

        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('admin_entity_permit_role');
        $subSelect->from(Entity\Permit\Role::class, 'admin_entity_permit_role');
        $subSelect->join('admin_entity_permit_role.entity', 'admin_entity_permit_entity');
        $subSelect->andWhere('admin_entity_permit_role.role = ?3');
        $subSelect->andWhere('admin_entity_permit_entity.underscoreFullEntityName = ?4');

        $qb->setParameter(3, $role);
        $qb->setParameter(4, $entity);

        $qb->setMaxResults(1);
        $qb->andWhere($qb->expr()->in('admin_entity_permit_user.role', $subSelect->getDQL()));

        return null !== $qb->getQuery()->getOneOrNullResult();
    }

    public function userHasGeneralPermit(Entity\User $user, string $role, string $entityName): bool
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_permit_user');
        $qb->from(Entity\Permit\User::class, 'admin_entity_permit_user');
        $qb->andWhere('admin_entity_permit_user.user = ?1');
        $qb->setParameter(1, $user);

        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('admin_entity_permit_role');
        $subSelect->from(Entity\Permit\Role::class, 'admin_entity_permit_role');
        $subSelect->join('admin_entity_permit_role.entity', 'admin_entity_permit_entity');
        $subSelect->andWhere('admin_entity_permit_role.role = ?3');
        $subSelect->andWhere('admin_entity_permit_entity.underscoreFullEntityName = ?4');

        $qb->setParameter(3, $role);
        $qb->setParameter(4, $entityName);

        $qb->setMaxResults(1);
        $qb->andWhere($qb->expr()->in('admin_entity_permit_user.role', $subSelect->getDQL()));

        return null !== $qb->getQuery()->getOneOrNullResult();
    }

    public function findPermitUserByUser(Entity\User $user): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(['admin_entity_permit_role AS role', 'COUNT(DISTINCT user.keyId) AS amountOfKeys']);
        $qb->from(Entity\Permit\Role::class, 'admin_entity_permit_role');
        $qb->join('admin_entity_permit_role.user', 'admin_entity_user');
        $qb->andWhere('admin_entity_user.user = :user');
        $qb->addGroupBy('admin_entity_permit_role.id');
        $qb->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }

    public function findPermitUserByEntityAndId(Entity\Permit\Entity $entity, int $id): array
    {
        $setters = [];
        foreach ($entity->getSetter() as $setter) {
            $setters[] = $setter->getId();
        }
        $accessRoles = [];
        foreach ($entity->getRole() as $role) {
            foreach ($role->getAccessRole() as $accessRole) {
                $accessRoles[] = $accessRole->getId();
            }
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_permit_user');
        $qb->from(Entity\Permit\User::class, 'admin_entity_permit_user');
        $qb->leftJoin('admin_entity_permit_user.setter', 'admin_entity_permit_setter');
        $qb->leftJoin('admin_entity_permit_user.accessRole', 'admin_entity_role');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->in('admin_entity_permit_setter.id', $setters),
                $qb->expr()->in('admin_entity_role.id', array_unique($accessRoles))
            )
        );

        $qb->andWhere('admin_entity_permit_user.keyId = :keyId');
        $qb->setParameter('keyId', $id);

        return $qb->getQuery()->getResult();
    }

    public function findUsersByEntityAndRoleAndId(
        Entity\Permit\Entity $entity,
        Entity\Permit\Role $permitRole,
        int $id
    ): array {
        $setters = [];
        foreach ($entity->getSetter() as $setter) {
            $setters[] = $setter->getId();
        }
        $accessRoles = [];
        foreach ($entity->getRole() as $role) {
            foreach ($role->getAccessRole() as $accessRole) {
                $accessRoles[] = $accessRole->getId();
            }
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_permit_user');
        $qb->from(Entity\Permit\User::class, 'admin_entity_permit_user');
        $qb->leftJoin('admin_entity_permit_user.setter', 'admin_entity_permit_setter');
        $qb->leftJoin('admin_entity_permit_user.accessRole', 'admin_entity_role');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->in('admin_entity_permit_setter.id', $setters),
                $qb->expr()->in('admin_entity_role.id', array_unique($accessRoles))
            )
        );

        $qb->andWhere('admin_entity_permit_user.keyId = :keyId');
        $qb->andWhere('admin_entity_permit_user.role = :role');
        $qb->setParameter('keyId', $id);
        $qb->setParameter('role', $permitRole);

        return $qb->getQuery()->getResult();
    }

    public function truncateTable(): void
    {
        $truncateQuery = 'TRUNCATE TABLE admin_permit_user';
        $this->_em->getConnection()->executeQuery($truncateQuery);
    }
}
