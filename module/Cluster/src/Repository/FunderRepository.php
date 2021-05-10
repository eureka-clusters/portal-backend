<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Repository;

use Cluster\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class Funder
 * @package Cluster\Repository
 */
class FunderRepository extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_funder');
        $queryBuilder->from(Entity\Funder::class, 'cluster_entity_funder');
        $queryBuilder->join('cluster_entity_funder.user', 'admin_entity_user');
        $queryBuilder->join('cluster_entity_funder.country', 'cluster_entity_country');

        $direction = 'DESC';
        if (
            isset($filter['direction'])
            && \in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)
        ) {
            $direction = strtoupper($filter['direction']);
        }

        if (null !== $filter) {
            $queryBuilder = $this->applyFilter($queryBuilder, $filter);
        }

        switch ($filter['order']) {
            case 'user':
                $queryBuilder->addOrderBy('admin_entity_user.lastName', $direction);
                break;
            case 'country':
                $queryBuilder->addOrderBy('cluster_entity_country.country', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('cluster_entity_funder.country', 'ASC');
        }

        return $queryBuilder;
    }

    public function applyFilter(QueryBuilder $queryBuilder, array $filter): QueryBuilder
    {
        if (!empty($filter['search'])) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('admin_entity_user.firstName', ':like'),
                    $queryBuilder->expr()->like('admin_entity_user.lastName', ':like'),
                    $queryBuilder->expr()->like('admin_entity_user.email', ':like'),
                    $queryBuilder->expr()->like('cluster_entity_country.country', ':like')
                )
            );

            $queryBuilder->setParameter('like', sprintf("%%%s%%", $filter['search']));
        }


        if (!empty($filter['showOnWebsite'])) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    'cluster_entity_funder.showOnWebsite',
                    $filter['showOnWebsite']
                )
            );
        }

        return $queryBuilder;
    }
}
