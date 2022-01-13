<?php

declare(strict_types=1);

namespace Cluster\Repository;

use Cluster\Entity\Organisation;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class OrganisationRepository extends EntityRepository
{
    public function getOrganisationsByFilter(array $filter): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_organisation');
        $queryBuilder->from(Organisation::class, 'cluster_entity_organisation');

        $this->applyFilters($filter, $queryBuilder);

        return $queryBuilder;
    }

    private function applyFilters(array $filter, QueryBuilder $queryBuilder): void
    {
    }
}
