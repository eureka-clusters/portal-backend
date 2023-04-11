<?php

declare(strict_types=1);

namespace Cluster\Repository;

use Cluster\Entity\Organisation;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\MatchAgainst;
use Jield\Search\ValueObject\SearchFormResult;

class OrganisationRepository extends EntityRepository
{
    public function getOrganisationsByFilter(
        SearchFormResult $searchFormResult,
    ): QueryBuilder {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'cluster_entity_organisation');
        $queryBuilder->from(from: Organisation::class, alias: 'cluster_entity_organisation');
        $queryBuilder->innerJoin(join: 'cluster_entity_organisation.partners', alias: 'cluster_entity_partners');

        $this->applyFilters(filter: $searchFormResult->getFilter(), queryBuilder: $queryBuilder);
        $this->applySorting(searchFormResult: $searchFormResult, queryBuilder: $queryBuilder);

        return $queryBuilder;
    }

    private function applyFilters(array $filter, QueryBuilder $queryBuilder): void
    {
    }

    private function applySorting(SearchFormResult $searchFormResult, QueryBuilder $queryBuilder): void
    {
        switch ($searchFormResult->getOrder()) {
            case 'id':
                $sortColumn = 'cluster_entity_organisation.id';
                break;
            case 'name':
            default:
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

        $queryBuilder->orderBy(sort: $sortColumn, order: $searchFormResult->getDirection());
    }

    public function searchOrganisations(
        ?string $query,
        int $limit
    ): QueryBuilder {
        $config = $this->_em->getConfiguration();
        $config->addCustomStringFunction(name: 'match_against', className: MatchAgainst::class);

        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'cluster_entity_organisation');
        $queryBuilder->from(from: Organisation::class, alias: 'cluster_entity_organisation');

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
