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

use Doctrine\ORM\EntityRepository;
use Cluster\Entity;
use Doctrine\ORM\QueryBuilder;

/**
 *
 */
class OrganisationRepository extends EntityRepository
{
    public function getOrganisationsByFilter(array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_organisation');
        $queryBuilder->from(Entity\Organisation::class, 'cluster_entity_organisation');

        $this->applyFilters($filter, $queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }

    private function applyFilters(array $filter, QueryBuilder $queryBuilder): void
    {

    }
}
