<?php

declare(strict_types=1);

namespace Cluster\Repository;

use Admin\Entity\User;
use Application\Repository\FilteredObjectRepository;
use Cluster\Entity\Cluster;
use Cluster\Entity\Country;
use Cluster\Entity\Organisation\Type;
use Cluster\Entity\Project;
use Cluster\Entity\Project\Partner;
use Cluster\Entity\Project\Status;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\MatchAgainst;
use Jield\Search\ValueObject\SearchFormResult;
use function array_map;
use function count;
use function is_countable;

class ProjectRepository extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'cluster_entity_project');
        $qb->from(from: Project::class, alias: 'cluster_entity_project');

        $qb = $this->applyProjectFilter(qb: $qb, searchFormResult: $searchFormResult);

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'id':
                $qb->addOrderBy(sort: 'cluster_entity_project.id', order: $direction);
                break;
            case 'name':
                $qb->addOrderBy(sort: 'cluster_entity_project.description', order: $direction);
                break;
            default:
                $qb->addOrderBy(sort: 'cluster_entity_project.description', order: Criteria::ASC);
        }

        return $qb;
    }

    public function applyProjectFilter(QueryBuilder $qb, SearchFormResult $searchFormResult): QueryBuilder
    {
        if ($searchFormResult->hasQuery()) {
            $qb->andWhere($qb->expr()->like(x: 'cluster_entity_project.description', y: ':like'));
            $qb->setParameter(key: 'like', value: sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        return $qb;
    }


    public function getProjectsByUserAndFilter(
        User             $user,
        SearchFormResult $searchFormResult,
    ): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'cluster_entity_project');
        $queryBuilder->from(from: Project::class, alias: 'cluster_entity_project');

        $this->applyFilters(filter: $searchFormResult->getFilter(), queryBuilder: $queryBuilder);
        $this->applySorting(searchFormResult: $searchFormResult, queryBuilder: $queryBuilder);

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder;
    }

    private function applyFilters(array $filter, QueryBuilder $queryBuilder): void
    {
        //Filters filters filters
        $countryFilter = $filter['country'] ?? [];

        if (!empty($countryFilter)) {
            switch ($filter['countryMethod'] ?? 'or') {
                case 'and':
                    //Find the projects where the country is active
                    $countryFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select(select: 'cluster_entity_project_filter_country')
                        ->from(from: Partner::class, alias: 'cluster_entity_project_partner_filter_country')
                        ->join(
                            join: 'cluster_entity_project_partner_filter_country.project',
                            alias: 'cluster_entity_project_filter_country'
                        )
                        ->join(
                            join: 'cluster_entity_project_partner_filter_country.organisation',
                            alias: 'cluster_entity_project_partner_filter_country_organisation'
                        )
                        ->join(
                            join: 'cluster_entity_project_partner_filter_country_organisation.country',
                            alias: 'cluster_entity_country'
                        )
                        ->where(
                            predicates: $queryBuilder->expr()->in(
                                x: 'cluster_entity_country.id',
                                y: $countryFilter
                            )
                        )
                        ->andWhere(
                            $queryBuilder->expr()->eq(
                                x: 'cluster_entity_project_partner_filter_country.isActive',
                                y: $queryBuilder->expr()->literal(literal: true)
                            )
                        )
                        ->addGroupBy(
                            groupBy: 'cluster_entity_project_filter_country.id'
                        ) //Add an id so we don't get all group by statements
                        ->having(
                            having: 'COUNT(DISTINCT cluster_entity_country) > ' . ((is_countable(
                                    value: $countryFilter
                                ) ? count(
                                    $countryFilter
                                ) : 0) - 1)
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in(x: 'cluster_entity_project', y: $countryFilterSubSelect->getDQL())
                    );

                    break;
                case 'or':
                    //Find the projects where the country is active
                    $countryFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select(select: 'cluster_entity_project_filter_country')
                        ->from(from: Partner::class, alias: 'cluster_entity_project_partner_filter_country')
                        ->join(
                            join: 'cluster_entity_project_partner_filter_country.project',
                            alias: 'cluster_entity_project_filter_country'
                        )
                        ->join(
                            join: 'cluster_entity_project_partner_filter_country.organisation',
                            alias: 'cluster_entity_project_partner_filter_country_organisation'
                        )
                        ->join(
                            join: 'cluster_entity_project_partner_filter_country_organisation.country',
                            alias: 'cluster_entity_country'
                        )
                        ->where(
                            predicates: $queryBuilder->expr()->in(
                                x: 'cluster_entity_country.id',
                                y: $countryFilter
                            )
                        )
                        ->andWhere(
                            $queryBuilder->expr()->eq(
                                'cluster_entity_project_partner_filter_country.isActive',
                                $queryBuilder->expr()->literal(literal: true)
                            )
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in(x: 'cluster_entity_project', y: $countryFilterSubSelect->getDQL())
                    );

                    break;
            }
        }

        $organisationTypeFilter = $filter['organisationType'] ?? [];

        if (!empty($organisationTypeFilter)) {
            switch ($filter['organisationTypeMethod'] ?? 'or') {
                case 'and':
                    //Find the projects we have at least organisations with this type
                    $organisationTypeFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select(select: 'cluster_entity_project_filter_organisation_type')
                        ->from(from: Partner::class, alias: 'cluster_entity_project_partner_filter_organisation_type')
                        ->join(
                            join: 'cluster_entity_project_partner_filter_organisation_type.project',
                            alias: 'cluster_entity_project_filter_organisation_type'
                        )
                        ->join(
                            join: 'cluster_entity_project_partner_filter_organisation_type.organisation',
                            alias: 'cluster_entity_project_partner_filter_organisation_type_organisation'
                        )
                        ->join(
                            join: 'cluster_entity_project_partner_filter_organisation_type_organisation.type',
                            alias: 'cluster_entity_organisation_type'
                        )
                        ->where(
                            predicates: $queryBuilder->expr()->in(
                                x: 'cluster_entity_organisation_type.id',
                                y: $organisationTypeFilter
                            )
                        )
                        ->andWhere(
                            $queryBuilder->expr()->eq(
                                'cluster_entity_project_partner_filter_organisation_type.isActive',
                                $queryBuilder->expr()->literal(literal: true)
                            )
                        )
                        ->addGroupBy(groupBy: 'cluster_entity_project_filter_organisation_type.id')
                        ->having(
                            having: 'COUNT(DISTINCT cluster_entity_organisation_type) > ' . ((is_countable(
                                    value: $organisationTypeFilter
                                ) ? count(
                                    $organisationTypeFilter
                                ) : 0) - 1)
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in(
                            x: 'cluster_entity_project',
                            y: $organisationTypeFilterSubSelect->getDQL()
                        )
                    );

                    break;
                case 'or':
                    //Find the projects where we have organisations with this type
                    $organisationTypeFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select(select: 'cluster_entity_project_filter_organisation_type')
                        ->from(from: Partner::class, alias: 'cluster_entity_project_partner_filter_organisation_type')
                        ->join(
                            join: 'cluster_entity_project_partner_filter_organisation_type.project',
                            alias: 'cluster_entity_project_filter_organisation_type'
                        )
                        ->join(
                            join: 'cluster_entity_project_partner_filter_organisation_type.organisation',
                            alias: 'cluster_entity_project_partner_filter_organisation_type_organisation'
                        )
                        ->join(
                            join: 'cluster_entity_project_partner_filter_organisation_type_organisation.type',
                            alias: 'cluster_entity_project_partner_filter_organisation_type_organisation_type'
                        )
                        ->where(
                            predicates: $queryBuilder->expr()->in(
                                x: 'cluster_entity_project_partner_filter_organisation_type_organisation_type.id',
                                y: $organisationTypeFilter
                            )
                        )
                        ->andWhere(
                            $queryBuilder->expr()->eq(
                                'cluster_entity_project_partner_filter_organisation_type.isActive',
                                $queryBuilder->expr()->literal(literal: true)
                            )
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in(
                            x: 'cluster_entity_project',
                            y: $organisationTypeFilterSubSelect->getDQL()
                        )
                    );

                    break;
            }
        }

        $projectStatusFilter = $filter['projectStatus'] ?? [];

        if (!empty($projectStatusFilter)) {
            //Find the projects where we have organisations with this type
            $projectStatusFilterSubSelect = $this->_em->createQueryBuilder()
                ->select(select: 'cluster_entity_project_filter_project_status')
                ->from(from: Project::class, alias: 'cluster_entity_project_filter_project_status')
                ->join(
                    join: 'cluster_entity_project_filter_project_status.status',
                    alias: 'cluster_entity_project_filter_project_status_status'
                )
                ->where(
                    predicates: $queryBuilder->expr()->in(
                        x: 'cluster_entity_project_filter_project_status_status.id',
                        y: $projectStatusFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(x: 'cluster_entity_project', y: $projectStatusFilterSubSelect->getDQL())
            );
        }

        $programmeCallFilter = $filter['programmeCall'] ?? [];

        if (!empty($programmeCallFilter)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(x: 'cluster_entity_project.programmeCall', y: $programmeCallFilter)
            );
        }

        $clusterGroupsFilter = $filter['clusterGroups'] ?? [];

        if (!empty($clusterGroupsFilter)) {
            //Find the projects where we have organisations with this type
            $primaryClusterFilterSubSelect = $this->_em->createQueryBuilder()
                ->select(select: 'cluster_entity_project_filter_primary_cluster')
                ->from(from: Project::class, alias: 'cluster_entity_project_filter_primary_cluster')
                ->join(
                    join: 'cluster_entity_project_filter_primary_cluster.primaryCluster',
                    alias: 'cluster_entity_project_filter_primary_cluster_primary_cluster'
                )
                ->join(
                    join: 'cluster_entity_project_filter_primary_cluster_primary_cluster.groups',
                    alias: 'cluster_entity_project_filter_primary_cluster_primary_cluster_groups'
                )
                ->where(
                    predicates: $queryBuilder->expr()->in(
                        x: 'cluster_entity_project_filter_primary_cluster_primary_cluster_groups.id',
                        y: $clusterGroupsFilter
                    )
                );

            $secondaryClusterFilterSubSelect = $this->_em->createQueryBuilder()
                ->select(select: 'cluster_entity_project_filter_secondary_cluster')
                ->from(from: Project::class, alias: 'cluster_entity_project_filter_secondary_cluster')
                ->join(
                    join: 'cluster_entity_project_filter_secondary_cluster.secondaryCluster',
                    alias: 'cluster_entity_project_filter_secondary_cluster_secondary_cluster'
                )
                ->join(
                    join: 'cluster_entity_project_filter_secondary_cluster_secondary_cluster.groups',
                    alias: 'cluster_entity_project_filter_secondary_cluster_secondary_cluster_groups'
                )
                ->where(
                    predicates: $queryBuilder->expr()->in(
                        x: 'cluster_entity_project_filter_secondary_cluster_secondary_cluster_groups.id',
                        y: $clusterGroupsFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->in(x: 'cluster_entity_project', y: $primaryClusterFilterSubSelect->getDQL()),
                    $queryBuilder->expr()->in(
                        x: 'cluster_entity_project',
                        y: $secondaryClusterFilterSubSelect->getDQL()
                    ),
                )
            );
        }
    }

    private function applySorting(SearchFormResult $searchFormResult, QueryBuilder $queryBuilder): void
    {
        switch ($searchFormResult->getOrder()) {
            case 'number':
                $sortColumn = 'cluster_entity_project.number';
                break;
            case 'name':
                $sortColumn = 'cluster_entity_project.name';
                break;
            case 'primaryCluster':
                $sortColumn = 'primaryCluster.name';
                $queryBuilder->join(join: 'cluster_entity_project.primaryCluster', alias: 'primaryCluster');
                break;
            case 'programme':
                $sortColumn = 'cluster_entity_project.programme';
                break;
            case 'programmeCall':
                $sortColumn = 'cluster_entity_project.programmeCall';
                break;
            case 'secondaryCluster':
                $sortColumn = 'secondaryCluster.name';
                $queryBuilder->leftJoin(join: 'cluster_entity_project.secondaryCluster', alias: 'secondaryCluster');
                break;
            case 'status':
                $sortColumn = 'projectStatus.status';
                $queryBuilder->join(join: 'cluster_entity_project.status', alias: 'projectStatus');
                break;

            //todo: if the latest version column always only displays "latest" then sorting doesn't make sense
            case 'latestVersionType':
                $sortColumn = 'latestversion_type.type';
                $queryBuilder->leftJoin(
                    join: 'cluster_entity_project.versions',
                    alias: 'latestversion',
                    conditionType: 'WITH',
                    condition: 'latestversion.type = 3'
                );
                $queryBuilder->join(join: 'latestversion.type', alias: 'latestversion_type');
                break;

            //todo how can the id of the latest version type be selected dynamically? or is this a fixed id
            case 'latestVersionTotalCosts':
                $sortColumn = 'latestversion.costs';
                $queryBuilder->leftJoin(
                    join: 'cluster_entity_project.versions',
                    alias: 'latestversion',
                    conditionType: 'WITH',
                    condition: 'latestversion.type = 3'
                );
                break;
            case 'latestVersionTotalEffort':
                $sortColumn = 'latestversion.effort';
                $queryBuilder->leftJoin(
                    join: 'cluster_entity_project.versions',
                    alias: 'latestversion',
                    conditionType: 'WITH',
                    condition: 'latestversion.type = 3'
                );
                break;
            default:
                $sortColumn = 'cluster_entity_project.labelDate';
                $searchFormResult->setDirection(direction: Criteria::DESC);
                break;
        }

        $queryBuilder->orderBy(sort: $sortColumn, order: $searchFormResult->getDirection());
    }

    private function applyUserFilter(QueryBuilder $queryBuilder, User $user): void
    {
        //Short-circuit this function when the user is member of eureka secretariat
        if ($user->isEurekaSecretariatStaffMember()) {
            return;
        }

        //Find the projects where the country is active
        $funderSubSelect = $this->_em->createQueryBuilder()
            ->select(select: 'cluster_entity_project_funder')
            ->from(from: Project::class, alias: 'cluster_entity_project_funder')
            ->join(join: 'cluster_entity_project_funder.partners', alias: 'cluster_entity_project_funder_partners')
            ->join(
                join: 'cluster_entity_project_funder_partners.organisation',
                alias: 'cluster_entity_project_funder_partners_organisation'
            )
            ->andWhere('cluster_entity_project_funder_partners_organisation.country = :funder_country')
            ->andWhere(
                $queryBuilder->expr()->eq(
                    x: 'cluster_entity_project_funder_partners.isActive',
                    y: $queryBuilder->expr()->literal(literal: true)
                )
            );

        $queryBuilder->andWhere(
            $queryBuilder->expr()->in(x: 'cluster_entity_project', y: $funderSubSelect->getDQL())
        );

        //Create an empty country to have a valid query
        $country = (new Country())->setId(id: 0);

        //When the user is a funder we can use the country of the funder
        if ($user->isFunder()) {
            $country = $user->getFunder()?->getCountry();
        }

        $queryBuilder->setParameter(key: 'funder_country', value: $country);
    }

    public function searchProjects(
        User   $user,
        string $query,
        int    $limit
    ): QueryBuilder
    {
        $config = $this->_em->getConfiguration();
        $config->addCustomStringFunction(name: 'match', className: MatchAgainst::class);

        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(
            select: 'cluster_entity_project'
        );
        $queryBuilder->from(from: Project::class, alias: 'cluster_entity_project');

        $queryBuilder->addSelect(
            select: 'MATCH (cluster_entity_project.number, 
            cluster_entity_project.name, 
            cluster_entity_project.title, 
            cluster_entity_project.description) AGAINST (:query IN BOOLEAN MODE) as score'
        );
        $queryBuilder->andWhere(
            'MATCH (cluster_entity_project.number, 
            cluster_entity_project.name, 
            cluster_entity_project.title, 
            cluster_entity_project.description) AGAINST (:query IN BOOLEAN MODE) > 0'
        );
        $queryBuilder->setParameter(key: 'query', value: '"%' . $query . '%"');

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        $queryBuilder->setMaxResults(maxResults: $limit);

        return $queryBuilder;
    }

    public function findProjectBySlugAndUser(string $slug, User $user): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'cluster_entity_project');
        $queryBuilder->from(from: Project::class, alias: 'cluster_entity_project');

        $queryBuilder->andWhere('cluster_entity_project.slug = :slug')->setParameter(key: 'slug', value: $slug);

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder;
    }

    public function fetchOrganisationTypes(User $user, SearchFormResult $searchFormResult): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_organisation_type.id',
            'cluster_entity_organisation_type.type',
            $queryBuilder->expr()->countDistinct(x: 'cluster_entity_project')
        );

        $queryBuilder->from(from: Type::class, alias: 'cluster_entity_organisation_type')
            ->join(
                join: 'cluster_entity_organisation_type.organisations',
                alias: 'cluster_entity_organisation_type_organisations'
            )
            ->join(
                join: 'cluster_entity_organisation_type_organisations.partners',
                alias: 'cluster_entity_organisation_type_organisations_partners'
            )
            ->join(
                join: 'cluster_entity_organisation_type_organisations_partners.project',
                alias: 'cluster_entity_project'
            )
            ->orderBy(sort: 'cluster_entity_organisation_type.type', order: Criteria::ASC)
            ->groupBy(groupBy: 'cluster_entity_organisation_type');

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchCountries(User $user, SearchFormResult $searchFormResult): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project_partners_organisation_country.country',
            'cluster_entity_project_partners_organisation_country.id',
            $queryBuilder->expr()->countDistinct(x: 'cluster_entity_project.id')
        );

        $queryBuilder->from(from: Project::class, alias: 'cluster_entity_project')
            ->join(join: 'cluster_entity_project.partners', alias: 'cluster_entity_project_partners')
            ->join(
                join: 'cluster_entity_project_partners.organisation',
                alias: 'cluster_entity_project_partners_organisation'
            )
            ->join(
                join: 'cluster_entity_project_partners_organisation.country',
                alias: 'cluster_entity_project_partners_organisation_country'
            )
            ->orderBy(sort: 'cluster_entity_project_partners_organisation_country.country', order: Criteria::ASC)
            ->groupBy(groupBy: 'cluster_entity_project_partners_organisation.country');

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchProgrammeCalls(User $user, SearchFormResult $searchFormResult): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project.programmeCall',
            $queryBuilder->expr()->count(x: 'cluster_entity_project.id')
        );

        $queryBuilder->from(from: Project::class, alias: 'cluster_entity_project')
            ->groupBy(groupBy: 'cluster_entity_project.programmeCall');

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        $queryBuilder->orderBy('cluster_entity_project.programmeCall', Criteria::ASC);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchClusterGroups(): array
    {
        // it should be a left join so that all clusters are returned even with 0 projects
        $queryBuilder = $this->_em->createQueryBuilder();

        // select primary
        $queryBuilder->select(
            'cluster_entity_cluster_group.id',
            'cluster_entity_cluster_group.name',
            $queryBuilder->expr()->count(x: 'cluster_entity_project.id'),
        );

        $queryBuilder->from(from: Cluster\Group::class, alias: 'cluster_entity_cluster_group')
            ->innerJoin(join: 'cluster_entity_cluster_group.clusters', alias: 'cluster_entity_cluster')
            ->innerJoin(join: 'cluster_entity_cluster.projectsPrimary', alias: 'cluster_entity_project')
            ->groupBy(groupBy: 'cluster_entity_cluster_group')
            ->orderBy(sort: 'cluster_entity_cluster_group.name', order: Criteria::ASC);

        $primaryClusters = $queryBuilder->getQuery()->getArrayResult();

        // select secondary
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(
            'cluster_entity_cluster_group.id',
            'cluster_entity_cluster_group.name',
            $queryBuilder->expr()->count(x: 'cluster_entity_project.id'),
        );

        $queryBuilder->from(from: Cluster\Group::class, alias: 'cluster_entity_cluster_group')
            ->innerJoin(join: 'cluster_entity_cluster_group.clusters', alias: 'cluster_entity_cluster')
            ->innerJoin(join: 'cluster_entity_cluster.projectsSecondary', alias: 'cluster_entity_project')
            ->groupBy(groupBy: 'cluster_entity_cluster_group')
            ->orderBy(sort: 'cluster_entity_cluster_group.name', order: Criteria::ASC);

        $secondaryClusters = $queryBuilder->getQuery()->getArrayResult();

        return array_map(static fn(array $cluster1, $cluster2) => [
            'id'   => $cluster1['id'],
            'name' => $cluster1['name'],
            1      => $cluster1[1],
            2      => $cluster2[1],
        ], $primaryClusters, $secondaryClusters);
    }

    public function fetchProjectStatuses(User $user, SearchFormResult $searchFormResult): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project_status.id',
            'cluster_entity_project_status.status',
            $queryBuilder->expr()->count(x: 'cluster_entity_project.id')
        );
        $queryBuilder->from(from: Status::class, alias: 'cluster_entity_project_status')
            ->join(join: 'cluster_entity_project_status.projects', alias: 'cluster_entity_project')
            ->groupBy(groupBy: 'cluster_entity_project_status');

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
