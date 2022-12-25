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
        string $sort = 'name',
        string $order = 'asc'
    ): QueryBuilder {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'cluster_entity_organisation');
        $queryBuilder->from(from: Organisation::class, alias: 'cluster_entity_organisation');

        $this->applyFilters(filter: $filter, queryBuilder: $queryBuilder);
        $this->applySorting(sort: $sort, order: $order, queryBuilder: $queryBuilder);

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
            case 'name':
                $sortColumn = 'cluster_entity_organisation.name';
                break;
            case 'country':
                $sortColumn = 'organisation_country.country';
                $queryBuilder->join(join: 'cluster_entity_organisation.country', alias: 'organisation_country');
                break;
            case 'type':
                $sortColumn = 'organisation_type.type';
                $queryBuilder->join(join: 'cluster_entity_organisation.type', alias: 'organisation_type');
                break;
        }

        if (isset($sortColumn)) {
            $queryBuilder->orderBy(sort: $sortColumn, order: $order);
        }
    }

    public function searchOrganisations(
        Funder $funder,
        string $query,
        int $limit
    ): QueryBuilder {
        $config = $this->_em->getConfiguration();
        $config->addCustomStringFunction(name: 'match_against', className: MatchAgainst::class);

        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'cluster_entity_organisation');
        $queryBuilder->from(from: Organisation::class, alias: 'cluster_entity_organisation');

        // $queryBuilder->andWhere(
        //     $queryBuilder->expr()->orX(
        //         $queryBuilder->expr()->like('cluster_entity_organisation.name', ':like'),
        //     )
        // );
        // $queryBuilder->setParameter('like', sprintf('%%%s%%', $query));

        $queryBuilder->addSelect(
            select: 'MATCH_AGAINST (cluster_entity_organisation.name) AGAINST (:match IN BOOLEAN MODE) as score'
        );
        $queryBuilder->andWhere(
            'MATCH_AGAINST (cluster_entity_organisation.name) AGAINST (:match IN BOOLEAN MODE) > 0'
        );
        $queryBuilder->setParameter(key: 'match', value: $query);

        $queryBuilder->setMaxResults(maxResults: $limit);

        return $queryBuilder;
    }
}
