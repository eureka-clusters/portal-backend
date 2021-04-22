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
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function in_array;
use function sprintf;

/**
 * Class Support
 *
 * @package Admin\Repository
 */
final class Support extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_support');
        $qb->from(Entity\Support::class, 'admin_entity_support');

        if (null !== $filter) {
            $qb = $this->applySupportFilter($qb, $filter);
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $qb->addOrderBy('admin_entity_support.name', $direction);
                break;
            case 'last-update':
                $qb->addOrderBy('admin_entity_support.lastUpdate', $direction);
                break;
            default:
                $qb->addOrderBy('admin_entity_support.name', $direction);
        }

        return $qb;
    }

    public function applySupportFilter(QueryBuilder $qb, array $filter): QueryBuilder
    {
        if (!empty($filter['query'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('admin_entity_support.name', ':like'),
                    $qb->expr()->like('admin_entity_support.description', ':like')
                )
            );
            $qb->setParameter('like', sprintf('%%%s%%', $filter['query']));
        }


        return $qb;
    }
}
