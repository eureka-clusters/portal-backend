<?php

declare(strict_types=1);

namespace Cluster\Repository;

use Cluster\Entity\Organisation;
use Cluster\Entity\Project\Partner;
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

        $queryBuilder->innerJoin(join: 'cluster_entity_organisation.type', alias: 'organisation_type');
        $queryBuilder->innerJoin(join: 'cluster_entity_organisation.country', alias: 'organisation_country');

        //We use the project database as filter
        //Create a sub query where we only have organisations that are in the project
        $subQuery = $this->_em->createQueryBuilder();
        $subQuery->select(select: 'cluster_entity_project_partner_organisation');
        $subQuery->from(from: Partner::class, alias: 'cluster_entity_project_partner');
        $subQuery->innerJoin(
            join: 'cluster_entity_project_partner.organisation',
            alias: 'cluster_entity_project_partner_organisation'
        );

        $queryBuilder->andWhere(
            $queryBuilder->expr()->in(
                x: 'cluster_entity_organisation',
                y: $subQuery->getDQL()
            )
        );

        $this->applySorting(searchFormResult: $searchFormResult, queryBuilder: $queryBuilder);

        return $queryBuilder;
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

                break;
            case 'type':
                $sortColumn = 'organisation_type.type';

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
