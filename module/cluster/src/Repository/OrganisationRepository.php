<?php

declare(strict_types=1);

namespace Cluster\Repository;

use Cluster\Entity\Funder;
use Cluster\Entity\Organisation;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\MatchAgainst;

class OrganisationRepository extends EntityRepository
{
    public function getOrganisationsByFilter(
        array $filter,
        string $sort = 'organisation.name',
        string $order = 'asc'
    ): QueryBuilder {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_organisation');
        $queryBuilder->from(Organisation::class, 'cluster_entity_organisation');

        $this->applyFilters($filter, $queryBuilder);
        $this->applySorting($sort, $order, $queryBuilder);

        return $queryBuilder;
    }

    private function applyFilters(array $filter, QueryBuilder $queryBuilder): void
    {
    }

    private function applySorting(string $sort, string $order, QueryBuilder $queryBuilder): void
    {
        $sortColumn = null;

        switch ($sort) {
            case 'id':
                $sortColumn = 'cluster_entity_organisation.id';
                break;
            case 'organisation.name':
                $sortColumn = 'cluster_entity_organisation.name';
                break;
            case 'organisation.country.country':
                $sortColumn = 'organisation_country.country';
                $queryBuilder->join('cluster_entity_organisation.country', 'organisation_country');
                break;
            case 'organisation.type.type':
                $sortColumn = 'organisation_type.type';
                $queryBuilder->join('cluster_entity_organisation.type', 'organisation_type');
                break;
        }

        if (isset($sortColumn)) {
            $queryBuilder->orderBy($sortColumn, $order);
        }
    }

    public function searchOrganisations(
        Funder $funder,
        string $query,
        int $limit
    ): QueryBuilder {

        $config = $this->_em->getConfiguration();
        $config->addCustomStringFunction('match_against', MatchAgainst::class);

        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_organisation');
        $queryBuilder->from(Organisation::class, 'cluster_entity_organisation');

        // $queryBuilder->andWhere(
        //     $queryBuilder->expr()->orX(
        //         $queryBuilder->expr()->like('cluster_entity_organisation.name', ':like'),
        //     )
        // );
        // $queryBuilder->setParameter('like', sprintf('%%%s%%', $query));


        $queryBuilder->addSelect('MATCH_AGAINST (cluster_entity_organisation.name) AGAINST (:match IN BOOLEAN MODE) as score');
        $queryBuilder->andWhere('MATCH_AGAINST (cluster_entity_organisation.name) AGAINST (:match IN BOOLEAN MODE) > 0');
        $queryBuilder->setParameter('match', $query);

        $queryBuilder->setMaxResults($limit);

        return $queryBuilder;
    }
}
