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

use Application\Repository\FilteredObjectRepository;
use Cluster\Entity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ClusterRepository
 * @package Cluster\Repository
 */
class ClusterRepository extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(array $filter = []): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('organisation_entity_cluster');
        $queryBuilder->from(Entity\Cluster::class, 'organisation_entity_cluster');

        if (array_key_exists('search', $filter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('organisation_entity_cluster.name', ':like'));
            $queryBuilder->setParameter('like', sprintf('%%%s%%', $filter['search']));
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(
                strtoupper($filter['direction']),
                [Criteria::ASC, Criteria::DESC],
                true
            )) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'name':
                $queryBuilder->addOrderBy('organisation_entity_cluster.name', $direction);
                break;
            case 'description':
                $queryBuilder->addOrderBy('organisation_entity_cluster.description', $direction);
                break;
            default:
                $queryBuilder->addOrderBy('organisation_entity_cluster.name', $direction);
        }

        return $queryBuilder;
    }
}
