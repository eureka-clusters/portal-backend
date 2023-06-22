<?php

declare(strict_types=1);

namespace Cluster\Repository\Project;

use Admin\Entity\User;
use Cluster\Entity\Cluster;
use Cluster\Entity\Country;
use Cluster\Entity\Organisation;
use Cluster\Entity\Organisation\Type;
use Cluster\Entity\Project;
use Cluster\Entity\Project\Partner;
use Cluster\Entity\Project\Status;
use Cluster\Entity\Project\Version\CostsAndEffort;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Jield\Search\ValueObject\SearchFormResult;
use function array_map;

class PartnerRepository extends EntityRepository
{
    public function getPartnersByUserAndFilter(
        User             $user,
        SearchFormResult $searchFormResult,
    ): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'project_partner');
        $queryBuilder->from(from: Partner::class, alias: 'project_partner');

        //We always need a join on project
        $queryBuilder->join(join: 'project_partner.organisation', alias: 'organisation');

        $this->activeInLatestVersionSubselect(queryBuilder: $queryBuilder);

        $queryBuilder->andWhere('project_partner.isActive = :isActive');
        $queryBuilder->setParameter(key: 'isActive', value: true);

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
            //Find the projects where the country is active
            $countryFilterSubSelect = $this->_em->createQueryBuilder()
                ->select(select: 'project_partner_filter_country')
                ->from(from: Partner::class, alias: 'project_partner_filter_country')
                ->join(
                    join: 'project_partner_filter_country.organisation',
                    alias: 'project_partner_filter_country_organisation'
                )
                ->join(join: 'project_partner_filter_country_organisation.country', alias: 'country')
                ->where(
                    predicates: $queryBuilder->expr()->in(
                        x: 'country.id',
                        y: $countryFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(x: 'project_partner', y: $countryFilterSubSelect->getDQL())
            );
        }

        $organisationTypeFilter = $filter['organisationType'] ?? [];

        if (!empty($organisationTypeFilter)) {
            //Find the projects where we have organisations with this type
            $organisationTypeFilterSubSelect = $this->_em->createQueryBuilder()
                ->select(select: 'project_partner_filter_organisation_type')
                ->from(from: Partner::class, alias: 'project_partner_filter_organisation_type')
                ->join(
                    join: 'project_partner_filter_organisation_type.organisation',
                    alias: 'project_partner_filter_organisation_type_organisation'
                )
                ->join(
                    join: 'project_partner_filter_organisation_type_organisation.type',
                    alias: 'project_partner_filter_organisation_type_organisation_type'
                )
                ->where(
                    predicates: $queryBuilder->expr()->in(
                        x: 'project_partner_filter_organisation_type_organisation_type.id',
                        y: $organisationTypeFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(x: 'project_partner', y: $organisationTypeFilterSubSelect->getDQL())
            );
        }

        $projectStatusFilter = $filter['projectStatus'] ?? [];

        if (!empty($projectStatusFilter)) {
            //Find the projects where we have organisations with this type
            $projectStatusFilterSubSelect = $this->_em->createQueryBuilder()
                ->select(select: 'project_partner_filter_project_status')
                ->from(from: Partner::class, alias: 'project_partner_filter_project_status')
                ->join(
                    join: 'project_partner_filter_project_status.project',
                    alias: 'project_partner_filter_project_status_project'
                )
                ->join(
                    join: 'project_partner_filter_project_status_project.status',
                    alias: 'project_partner_filter_project_status_project_status'
                )
                ->where(
                    predicates: $queryBuilder->expr()->in(
                        x: 'project_partner_filter_project_status_project_status.id',
                        y: $projectStatusFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(x: 'project_partner', y: $projectStatusFilterSubSelect->getDQL())
            );
        }

        $clusterGroupsFilter = $filter['clusterGroups'] ?? [];

        if (!empty($clusterGroupsFilter)) {
            //Find the projects where we have organisations with this type
            $primaryClusterFilterSubSelect = $this->_em->createQueryBuilder()
                ->select(select: 'project_partner_filter_primary_cluster')
                ->from(from: Partner::class, alias: 'project_partner_filter_primary_cluster')
                ->join(
                    join: 'project_partner_filter_primary_cluster.project',
                    alias: 'project_partner_filter_primary_cluster_project'
                )
                ->join(
                    join: 'project_partner_filter_primary_cluster_project.primaryCluster',
                    alias: 'project_partner_filter_primary_cluster_project_primary_cluster'
                )
                ->join(
                    join: 'project_partner_filter_primary_cluster_project_primary_cluster.groups',
                    alias: 'project_partner_filter_primary_cluster_project_primary_cluster_groups'
                )
                ->where(
                    predicates: $queryBuilder->expr()->in(
                        x: 'project_partner_filter_primary_cluster_project_primary_cluster_groups.id',
                        y: $clusterGroupsFilter
                    )
                );

            $secondaryClusterFilterSubSelect = $this->_em->createQueryBuilder()
                ->select(select: 'project_partner_filter_secondary_cluster')
                ->from(from: Partner::class, alias: 'project_partner_filter_secondary_cluster')
                ->join(
                    join: 'project_partner_filter_secondary_cluster.project',
                    alias: 'project_partner_filter_secondary_cluster_project'
                )
                ->join(
                    join: 'project_partner_filter_secondary_cluster_project.secondaryCluster',
                    alias: 'project_partner_filter_secondary_cluster_project_secondary_cluster'
                )
                ->join(
                    join: 'project_partner_filter_secondary_cluster_project_secondary_cluster.groups',
                    alias: 'project_partner_filter_secondary_cluster_project_secondary_cluster_groups'
                )
                ->where(
                    predicates: $queryBuilder->expr()->in(
                        x: 'project_partner_filter_secondary_cluster_project_secondary_cluster_groups.id',
                        y: $clusterGroupsFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->in(x: 'project_partner', y: $primaryClusterFilterSubSelect->getDQL()),
                    $queryBuilder->expr()->in(x: 'project_partner', y: $secondaryClusterFilterSubSelect->getDQL()),
                )
            );
        }

        $programmeCallFilter = $filter['programmeCall'] ?? [];

        if (!empty($programmeCallFilter)) {
            //Find the projects who are in the call
            $programmeCallFilterSubset = $this->_em->createQueryBuilder()
                ->select(select: 'project_partner_filter_programme_call')
                ->from(from: Partner::class, alias: 'project_partner_filter_programme_call')
                ->join(
                    join: 'project_partner_filter_programme_call.project',
                    alias: 'project_partner_filter_programme_call_project'
                )
                ->where(
                    predicates: $queryBuilder->expr()->in(
                        x: 'project_partner_filter_programme_call_project.programmeCall',
                        y: $programmeCallFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(x: 'project_partner', y: $programmeCallFilterSubset->getDQL())
            );
        }

        $yearFilter = $filter['year'] ?? [];

        if (!empty($yearFilter)) {
            $queryBuilder->select('project_partner', 'project_partner_costs_and_effort');
            //If we have a year, then we join on costs and effort
            $queryBuilder->join(join: 'project_partner.costsAndEffort', alias: 'project_partner_costs_and_effort');
            $queryBuilder->join(
                join: 'project_partner_costs_and_effort.version',
                alias: 'project_partner_costs_and_effort_version'
            );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(x: 'project_partner_costs_and_effort.year', y: $yearFilter)
            );
            $queryBuilder->andWhere('project_partner_costs_and_effort_version.type = :type');
            $queryBuilder->setParameter(key: 'type', value: (new Type())->setId(id: 3));
        }
    }

    private function applySorting(SearchFormResult $searchFormResult, QueryBuilder $queryBuilder): void
    {
        $sortColumn = null;

        switch ($searchFormResult->getOrder()) {
            case 'id':
                $sortColumn = 'project_partner.id';
                break;
            case 'organisation':
                $sortColumn = 'organisation.name';
                break;
            case 'country':
                $sortColumn = 'organisation_country.country';
                $queryBuilder->join(join: 'organisation.country', alias: 'organisation_country');
                break;
            case 'type':
                $sortColumn = 'organisation_type.type';
                $queryBuilder->join(join: 'organisation.type', alias: 'organisation_type');
                break;
            case 'project':
                $sortColumn = 'cluster_entity_project.name';
                break;
            case 'projectStatus':
                $sortColumn = 'cluster_entity_project_status.status';
                $queryBuilder->join(join: 'cluster_entity_project.status', alias: 'cluster_entity_project_status');
                break;
            case 'primaryCluster':
                $sortColumn = 'cluster_entity_project_primary_cluster.name';
                $queryBuilder->join(join: 'cluster_entity_project.primaryCluster', alias: 'cluster_entity_project_primary_cluster');
                break;
            case 'latestVersionCosts':
                $sortColumn = 'project_partner.latestVersionCosts';
                break;
            case 'latestVersionEffort':
                $sortColumn = 'project_partner.latestVersionEffort';
                break;
            case 'year':
                $sortColumn = 'project_partner_costs_and_effort.year';
                break;
            case 'latestVersionCostsInYear':
                $sortColumn = 'project_partner_costs_and_effort.costs';
                break;
            case 'latestVersionEffortInYear':
                $sortColumn = 'project_partner_costs_and_effort.effort';
                break;
        }

        if (isset($sortColumn)) {
            $queryBuilder->orderBy(sort: $sortColumn, order: $searchFormResult->getDirection());
        }
    }

    private function applyUserFilter(QueryBuilder $queryBuilder, User $user): void
    {
        /** Short-circuit the function when the user is member of Eureka Secretariat */
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
            ->andWhere('cluster_entity_project_funder_partners_organisation.country = :funder_country');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->in(x: 'cluster_entity_project', y: $funderSubSelect->getDQL())
        );

        //Create an empty country to have a valid query which will not give any result
        $country = (new Country())->setId(id: 0);

        //When the user is a funder we can use the country of the funder
        if ($user->isFunder()) {
            $country = $user->getFunder()?->getCountry();
        }

        $queryBuilder->setParameter(key: 'funder_country', value: $country);
    }

    public function getPartnersByProject(
        User             $user,
        Project          $project,
        SearchFormResult $searchFormResult
    ): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'project_partner');
        $queryBuilder->from(from: Partner::class, alias: 'project_partner');

        $queryBuilder->andWhere('project_partner.isActive = :isActive');
        $queryBuilder->setParameter(key: 'isActive', value: true);

        $this->activeInLatestVersionSubselect(queryBuilder: $queryBuilder, project: $project);

        $queryBuilder->join(join: 'project_partner.organisation', alias: 'organisation');

        //We always need a join on project
        $queryBuilder->join(join: 'project_partner.project', alias: 'cluster_entity_project');

        $this->applySorting(searchFormResult: $searchFormResult, queryBuilder: $queryBuilder);
        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder;
    }

    private function activeInLatestVersionSubselect(QueryBuilder $queryBuilder, ?Project $project = null): void
    {
        $activeInLatestVersionSubSelect = $this->_em->createQueryBuilder();
        $activeInLatestVersionSubSelect->select(select: 'project_partner_subselect');
        $activeInLatestVersionSubSelect->from(from: Partner::class, alias: 'project_partner_subselect');
        $activeInLatestVersionSubSelect->join(
            join: 'project_partner_subselect.project',
            alias: 'project_partner_subselect_project'
        );
        $activeInLatestVersionSubSelect->join(
            join: 'project_partner_subselect.costsAndEffort',
            alias: 'project_partner_subselect_costs_and_effort'
        );
        $activeInLatestVersionSubSelect->join(
            join: 'project_partner_subselect_costs_and_effort.version',
            alias: 'project_partner_subselect_costs_and_effort_version'
        );
        $activeInLatestVersionSubSelect->join(
            join: 'project_partner_subselect_costs_and_effort_version.type',
            alias: 'project_partner_subselect_costs_and_effort_version_type'
        );
        $activeInLatestVersionSubSelect->andWhere(
            'project_partner_subselect_costs_and_effort_version_type.type = :type'
        );

        if (null !== $project) {
            $activeInLatestVersionSubSelect->andWhere('project_partner_subselect_project.id = :projectId');
            $queryBuilder->setParameter(key: 'projectId', value: $project->getId());
        }

        $queryBuilder->setParameter(key: 'type', value: \Cluster\Entity\Version\Type::TYPE_LATEST);

        $queryBuilder->andWhere(
            $queryBuilder->expr()->in(x: 'project_partner', y: $activeInLatestVersionSubSelect->getDQL())
        );
    }

    public function getPartnersByOrganisation(
        User             $user,
        Organisation     $organisation,
        SearchFormResult $searchFormResult
    ): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'project_partner');
        $queryBuilder->from(from: Partner::class, alias: 'project_partner');
        $queryBuilder->join(join: 'project_partner.organisation', alias: 'project_partner_organisation');
        $queryBuilder->where(predicates: 'project_partner_organisation = :organisation');
        $queryBuilder->setParameter(key: 'organisation', value: $organisation);

        $queryBuilder->andWhere('project_partner.isActive = :isActive');
        $queryBuilder->setParameter(key: 'isActive', value: true);

        $this->activeInLatestVersionSubselect(queryBuilder: $queryBuilder);

        $this->applySorting(searchFormResult: $searchFormResult, queryBuilder: $queryBuilder);
        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder;
    }

    public function fetchCountries(User $user, SearchFormResult $searchFormResult): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_country.id',
            'cluster_entity_country.country',
            $queryBuilder->expr()->count(x: 'cluster_entity_project_partner.id')
        );

        $queryBuilder->from(from: Partner::class, alias: 'cluster_entity_project_partner');
        $queryBuilder->join(join: 'cluster_entity_project_partner.organisation', alias: 'cluster_entity_organisation');
        $queryBuilder->join(join: 'cluster_entity_organisation.country', alias: 'cluster_entity_country');
        $queryBuilder->orderBy(sort: 'cluster_entity_country.country', order: Criteria::ASC);
        $queryBuilder->groupBy(groupBy: 'cluster_entity_country');

        //Join on partner to have the funder filter
        $queryBuilder->join(join: 'cluster_entity_project_partner.project', alias: 'cluster_entity_project');
        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchOrganisationTypes(User $user, SearchFormResult $searchFormResult): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_organisation_type.id',
            'cluster_entity_organisation_type.type',
            $queryBuilder->expr()->count(x: 'cluster_entity_project_partner.id')
        );

        $queryBuilder->from(from: Type::class, alias: 'cluster_entity_organisation_type');
        $queryBuilder->join(join: 'cluster_entity_organisation_type.organisations', alias: 'cluster_entity_organisation');
        $queryBuilder->join(join: 'cluster_entity_organisation.partners', alias: 'cluster_entity_project_partner');
        $queryBuilder->orderBy(sort: 'cluster_entity_organisation_type.type', order: Criteria::ASC);
        $queryBuilder->groupBy(groupBy: 'cluster_entity_organisation_type');

        //Join on partner to have the funder filter
        $queryBuilder->join(join: 'cluster_entity_project_partner.project', alias: 'cluster_entity_project');

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

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
            $queryBuilder->expr()->count(x: 'cluster_project_primary_partner.id')
        );

        $queryBuilder->from(from: Cluster\Group::class, alias: 'cluster_entity_cluster_group');
        $queryBuilder->join(join: 'cluster_entity_cluster_group.clusters', alias: 'cluster_entity_cluster');
        $queryBuilder->leftJoin(join: 'cluster_entity_cluster.projectsPrimary', alias: 'cluster_entity_project');
        $queryBuilder->leftJoin(join: 'cluster_entity_project.partners', alias: 'cluster_project_primary_partner');
        $queryBuilder->groupBy(groupBy: 'cluster_entity_cluster_group');
        $queryBuilder->orderBy(sort: 'cluster_entity_cluster_group.name', order: Criteria::ASC);

        $primaryClusters = $queryBuilder->getQuery()->getArrayResult();

        // select secondary
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(
            'cluster_entity_cluster_group.id',
            'cluster_entity_cluster_group.name',
            $queryBuilder->expr()->count(x: 'cluster_project_secondary_partner.id')
        );

        $queryBuilder->from(from: Cluster\Group::class, alias: 'cluster_entity_cluster_group');
        $queryBuilder->join(join: 'cluster_entity_cluster_group.clusters', alias: 'cluster_entity_cluster');
        $queryBuilder->leftJoin(join: 'cluster_entity_cluster.projectsSecondary', alias: 'cluster_entity_project');
        $queryBuilder->leftJoin(join: 'cluster_entity_project.partners', alias: 'cluster_project_secondary_partner');
        $queryBuilder->groupBy(groupBy: 'cluster_entity_cluster_group');
        $queryBuilder->orderBy(sort: 'cluster_entity_cluster_group.name', order: Criteria::ASC);

        $secondaryClusters = $queryBuilder->getQuery()->getArrayResult();

        return array_map(static fn(array $cluster1, $cluster2) => [
            'name' => $cluster1['name'],
            'id'   => $cluster1['id'],
            '1'    => $cluster1[1],
            '2'    => $cluster2[1],
        ], $primaryClusters, $secondaryClusters);
    }

    public function fetchProgrammeCalls(User $user, SearchFormResult $searchFormResult): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project.programmeCall',
            $queryBuilder->expr()->count(x: 'project_partners.id')
        );

        $queryBuilder->from(from: Project::class, alias: 'cluster_entity_project');
        $queryBuilder->join(
            join: 'cluster_entity_project.partners',
            alias: 'project_partners'
        );

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        $queryBuilder->groupBy(groupBy: 'cluster_entity_project.programmeCall');
        $queryBuilder->orderBy(sort: 'cluster_entity_project.programmeCall', order: Criteria::ASC);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchProjectStatuses(User $user, SearchFormResult $searchFormResult): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project_status.id',
            'cluster_entity_project_status.status',
            $queryBuilder->expr()->count(x: 'cluster_entity_project_partners.id')
        );

        $queryBuilder->from(from: Status::class, alias: 'cluster_entity_project_status');
        $queryBuilder->join(join: 'cluster_entity_project_status.projects', alias: 'cluster_entity_project');
        $queryBuilder->join(
            join: 'cluster_entity_project.partners',
            alias: 'cluster_entity_project_partners'
        );

        //Join on partner to have the funder filter
//        $queryBuilder->join(join: 'project_status_project_partners.project', alias: 'cluster_entity_project');
        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        $queryBuilder->groupBy(groupBy: 'cluster_entity_project_status');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchYears(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(select: 'project_version_costs_and_effort.year')
            ->distinct(flag: true)
            ->from(from: CostsAndEffort::class, alias: 'project_version_costs_and_effort')
            ->orderBy(sort: 'project_version_costs_and_effort.year', order: Criteria::ASC);

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
