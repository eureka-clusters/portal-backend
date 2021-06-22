<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Admin\Repository;

use Admin\Entity;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use PDO;

use function array_key_exists;
use function in_array;
use function sprintf;

/**
 * Class User
 *
 * @package Admin\Repository
 */
final class User extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_user');
        $qb->from(Entity\User::class, 'admin_entity_user');

        if (null !== $filter) {
            $qb = $this->applyUserFilter($qb, $filter);
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)) {
            $direction = strtoupper($filter['direction']);
        }


        switch ($filter['order']) {
            case 'id':
                $qb->addOrderBy('admin_entity_user.id', $direction);
                break;
            case 'name':
                $qb->addOrderBy('admin_entity_user.lastName', $direction);
                break;
            case 'username':
                $qb->addOrderBy('admin_entity_user.userPrincipalName', $direction);
                break;
            case 'email':
                $qb->addOrderBy('admin_entity_user.email', $direction);
                break;
            case 'add-date':
                $qb->addOrderBy('admin_entity_user.dateCreated', $direction);
                break;
            case 'last-update':
                $qb->addOrderBy('admin_entity_user.lastUpdate', $direction);
                break;
            default:
                $qb->addOrderBy('admin_entity_user.username', $direction);
        }

        return $qb;
    }

    public function applyUserFilter(QueryBuilder $qb, array $filter): QueryBuilder
    {
        if (!empty($filter['query'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('admin_entity_user.username', ':like'),
                    $qb->expr()->like('admin_entity_user.firstName', ':like'),
                    $qb->expr()->like('admin_entity_user.lastName', ':like'),
                    $qb->expr()->like('admin_entity_user.email', ':like')
                )
            );
            $qb->setParameter('like', sprintf('%%%s%%', $filter['query']));
        }

        //Go over the filterArray and apply the filters which are there
        if (array_key_exists('status', $filter['filter'])) {
            $qb->andWhere($qb->expr()->in('admin_entity_user.status', $filter['filter']['status']));
        }

        //Go over the filterArray and apply the filters which are there
        if (array_key_exists('roles', $filter['filter'])) {
            $qb->join('admin_entity_user.roles', 'roles');
            $qb->andWhere($qb->expr()->in('roles.id', $filter['filter']['roles']));
        }

        return $qb;
    }

    public function searchUserByName(string $name): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_user');
        $qb->from(Entity\User::class, 'admin_entity_user');

        $qb->andWhere($qb->expr()->like('admin_entity_user.displayName', ':like'));
        $qb->setParameter('like', sprintf('%%%s%%', $name));

        return $qb->getQuery()->getArrayResult();
    }
}
