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
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function in_array;

/**
 * Class Selection
 *
 * @package Admin\Repository
 */
final class Selection extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_selection');
        $qb->from(Entity\Selection::class, 'admin_entity_selection');

        if (array_key_exists('search', $filter)) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(
                        'admin_entity_selection.selection',
                        ':like'
                    ),
                    $qb->expr()->like('admin_entity_selection.tag', ':like')
                )
            );


            $qb->setParameter('like', sprintf('%%%s%%', $filter['query']));
        }

        if (array_key_exists('sql', $filter)) {
            $qb->join('admin_entity_selection.sql', 'sql');
        }

        if (array_key_exists('tags', $filter)) {
            $qb->andWhere($qb->expr()->in('admin_entity_selection.tag', $filter['tags']));
        }

        if (!array_key_exists('includeDeleted', $filter)) {
            //Do not show the deleted ones
            $qb->andWhere($qb->expr()->isNull('admin_entity_selection.dateEnd'));
        }

        if (array_key_exists('core', $filter)) {
            $qb->andWhere($qb->expr()->in('admin_entity_selection.tag', $filter['core']));
        }

        $direction = Criteria::ASC;
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), [Criteria::ASC, Criteria::DESC], true)) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $qb->addOrderBy('admin_entity_selection.selection', $direction);
                break;
            case 'tag':
                $qb->addOrderBy('admin_entity_selection.tag', $direction);
                break;
            case 'core':
                $qb->addOrderBy('admin_entity_selection.core', $direction);
                break;
            case 'owner':
                $qb->join('admin_entity_selection.user', 'user');
                $qb->addOrderBy('user.lastName', $direction);
                break;
            case 'date':
                $qb->addOrderBy('admin_entity_selection.dateCreated', $direction);
                break;
            default:
                $qb->addOrderBy('admin_entity_selection.id', $direction);
        }

        return $qb;
    }

    public function findSqlSelections(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_selection');
        $qb->from(Entity\Selection::class, 'admin_entity_selection');
        $qb->innerJoin('admin_entity_selection.sql', 'admin_entity_selection_sql');
        $qb->orderBy('admin_entity_selection.selection', Criteria::ASC);

        $qb->andWhere($qb->expr()->isNull('admin_entity_selection.dateEnd'));

        return $qb->getQuery()->getResult();
    }

    public function findNonSqlSelections(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_selection');
        $qb->from(Entity\Selection::class, 'admin_entity_selection');
        $qb->orderBy('admin_entity_selection.selection', Criteria::ASC);

        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('admin_entity_selection_sub.id');
        $subSelect->from(Entity\Selection\Sql::class, 'admin_entity_selection_sql');
        $subSelect->join('admin_entity_selection_sql.selection', 'admin_entity_selection_sub');

        $qb->andWhere($qb->expr()->notIn('admin_entity_selection.id', $subSelect->getDQL()));

        $qb->andWhere($qb->expr()->isNull('admin_entity_selection.dateEnd'));

        return $qb->getQuery()->getResult();
    }

    public function findTags(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_selection.tag');
        $qb->distinct();
        $qb->from(Entity\Selection::class, 'admin_entity_selection');
        $qb->orderBy('admin_entity_selection.tag', Criteria::ASC);

        $qb->andWhere($qb->expr()->isNull('admin_entity_selection.dateEnd'));

        return $qb->getQuery()->getArrayResult();
    }

    public function findActive(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_selection');
        $qb->from(Entity\Selection::class, 'admin_entity_selection');
        $qb->orderBy('admin_entity_selection.selection', Criteria::ASC);

        $qb->andWhere($qb->expr()->isNull('admin_entity_selection.dateEnd'));

        return $qb->getQuery()->getResult();
    }

    public function findFixedSelectionsByUser(Entity\User $user): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_selection');
        $qb->from(Entity\Selection::class, 'admin_entity_selection');

        $subSelect = $this->_em->createQueryBuilder();
        $subSelect->select('selection.id');
        $subSelect->from(Entity\Selection\User::class, 'admin_entity_selection_user');
        $subSelect->join('admin_entity_selection_user.user', 'user');
        $subSelect->join('admin_entity_selection_user.selection', 'selection');
        $subSelect->where('user = :user');
        $qb->setParameter('user', $user);

        $qb->andWhere($qb->expr()->in('admin_entity_selection.id', $subSelect->getDQL()));

        return $qb->getQuery()->useQueryCache(true)->getResult();
    }
}
