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

use function array_map;

class PartnerRepository extends EntityRepository
{
    public function getPartnersByUserAndFilter(
        User $user,
        array $filter,
        string $sort = 'partner.organisation.name',
        string $order = 'asc'
    ): QueryBuilder {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'project_partner');
        $queryBuilder->from(from: Partner::class, alias: 'project_partner');

        //We always need a join on project
        $queryBuilder->join(join: 'project_partner.project', alias: 'cluster_entity_project');

        $this->applyFilters(filter: $filter, queryBuilder: $queryBuilder);
        $this->applySorting(sort: $sort, order: $order, queryBuilder: $queryBuilder);

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder;
    }

    private function applyFilters(array $filter, QueryBuilder $queryBuilder): void
    {
        //Filters filters filters
        $countryFilter = $filter['country'] ?? [];

        if (! empty($countryFilter)) {
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
                        x: 'country.country',
                        y: $countryFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(x: 'project_partner', y: $countryFilterSubSelect->getDQL())
            );
        }

        $organisationTypeFilter = $filter['organisationType'] ?? [];

        if (! empty($organisationTypeFilter)) {
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
                        x: 'project_partner_filter_organisation_type_organisation_type.type',
                        y: $organisationTypeFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(x: 'project_partner', y: $organisationTypeFilterSubSelect->getDQL())
            );
        }

        $projectStatusFilter = $filter['projectStatus'] ?? [];

        if (! empty($projectStatusFilter)) {
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
                        x: 'project_partner_filter_project_status_project_status.status',
                        y: $projectStatusFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(x: 'project_partner', y: $projectStatusFilterSubSelect->getDQL())
            );
        }

        $clustersFilter = $filter['clusters'] ?? [];

        if (! empty($clustersFilter)) {
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
                ->where(
                    predicates: $queryBuilder->expr()->in(
                        x: 'project_partner_filter_primary_cluster_project_primary_cluster.name',
                        y: $clustersFilter
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
                ->where(
                    predicates: $queryBuilder->expr()->in(
                        x: 'project_partner_filter_secondary_cluster_project_secondary_cluster.name',
                        y: $clustersFilter
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

        if (! empty($programmeCallFilter)) {
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

        if (! empty($yearFilter)) {
            $queryBuilder->select('project_partner', 'project_partner_costs_and_effort');
            //If we have a year, then we join on costs and effort
            $queryBuilder->join(join: 'project_partner.costsAndEffort', alias: 'project_partner_costs_and_effort');
            $queryBuilder->join(
                join: 'project_partner_costs_and_effort.version',
                alias: 'project_partner_costs_and_effort_version'
            );

            $queryBuilder->andWhere($queryBuilder->expr()->in(x: 'project_partner_costs_and_effort.year', y: $yearFilter));
            $queryBuilder->andWhere('project_partner_costs_and_effort_version.type = :type');
            $queryBuilder->setParameter(key: 'type', value: (new Type())->setId(id: 3));
        }
    }

    private function applySorting(string $sort, string $order, QueryBuilder $queryBuilder): void
    {
        $sortColumn = null;

        switch ($sort) {
            case 'partner.id':
                $sortColumn = 'project_partner.id';
                break;
            case 'partner.project.name':
                $sortColumn = 'cluster_entity_project.name';
                break;
            case 'partner.organisation.name':
                $sortColumn = 'organisation.name';
                $queryBuilder->join(join: 'project_partner.organisation', alias: 'organisation');
                break;
            case 'partner.organisation.country.country':
                $sortColumn = 'organisation_country.country';
                $queryBuilder->join(join: 'project_partner.organisation', alias: 'organisation');
                $queryBuilder->join(join: 'organisation.country', alias: 'organisation_country');
                break;
            case 'partner.organisation.type.type':
                $sortColumn = 'organisation_type.type';
                $queryBuilder->join(join: 'project_partner.organisation', alias: 'organisation');
                $queryBuilder->join(join: 'organisation.type', alias: 'organisation_type');
                break;
            case 'partner.latestVersionCosts':
                $sortColumn = 'project_partner.latestVersionCosts';
                break;
            case 'partner.latestVersionEffort':
                $sortColumn = 'project_partner.latestVersionEffort';
                break;
            case 'partner.year':
                $sortColumn = 'project_partner_costs_and_effort.year';
                break;
            case 'partner.latestVersionCostsInYear':
                $sortColumn = 'project_partner_costs_and_effort.costs';
                break;
            case 'partner.latestVersionEffortInYear':
                $sortColumn = 'project_partner_costs_and_effort.effort';
                break;
        }

        if (isset($sortColumn)) {
            $queryBuilder->orderBy(sort: $sortColumn, order: $order);
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

    public function getPartnersByProject(Project $project): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'project_partner');
        $queryBuilder->from(from: Partner::class, alias: 'project_partner');

        $this->activeInLatestVersionSubselect(queryBuilder: $queryBuilder, project: $project);

        $queryBuilder->join(join: 'project_partner.organisation', alias: 'organisation');
        $queryBuilder->addOrderBy(sort: 'organisation.name');

        return $queryBuilder;
    }

    private function activeInLatestVersionSubselect(QueryBuilder $queryBuilder, Project $project): void
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
        $activeInLatestVersionSubSelect->andWhere('project_partner_subselect_project.id = :projectId');

        $queryBuilder->setParameter(key: 'type', value: \Cluster\Entity\Version\Type::TYPE_LATEST);
        $queryBuilder->setParameter(key: 'projectId', value: $project->getId());
        $queryBuilder->andWhere(
            $queryBuilder->expr()->in(x: 'project_partner', y: $activeInLatestVersionSubSelect->getDQL())
        );
    }

    public function getPartnersByOrganisation(Organisation $organisation): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(select: 'project_partner');
        $queryBuilder->from(from: Partner::class, alias: 'project_partner');
        $queryBuilder->join(join: 'project_partner.organisation', alias: 'project_partner_organisation');
        $queryBuilder->where(predicates: 'project_partner_organisation = :organisation');
        $queryBuilder->setParameter(key: 'organisation', value: $organisation);
        $queryBuilder->addOrderBy(sort: 'project_partner_organisation.name');

        return $queryBuilder;
    }

    public function fetchCountries(User $user, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'country.country',
            $queryBuilder->expr()->count(x: 'project_partner.id')
        );

        $queryBuilder->from(from: Partner::class, alias: 'project_partner');
        $queryBuilder->join(join: 'project_partner.organisation', alias: 'organisation');
        $queryBuilder->join(join: 'organisation.country', alias: 'country');
        $queryBuilder->groupBy(groupBy: 'country');

        //Join on partner to have the funder filter
        $queryBuilder->join(join: 'project_partner.project', alias: 'cluster_entity_project');
        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchOrganisationTypes(User $user, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'organisation_type.type',
            $queryBuilder->expr()->count(x: 'organisation_partners.id')
        );

        $queryBuilder->from(from: Type::class, alias: 'organisation_type');
        $queryBuilder->join(join: 'organisation_type.organisations', alias: 'organisation');
        $queryBuilder->join(join: 'organisation.partners', alias: 'organisation_partners');
        $queryBuilder->groupBy(groupBy: 'organisation_type');

        //Join on partner to have the funder filter
        $queryBuilder->join(join: 'organisation_partners.project', alias: 'cluster_entity_project');

        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchClusters(): array
    {
        // it should be a left join so that all clusters are returned even with 0 projects
        $queryBuilder = $this->_em->createQueryBuilder();

        // select primary
        $queryBuilder->select(
            'cluster.name',
            $queryBuilder->expr()->count(x: 'cluster_project_primary_partner.id')
        );

        $queryBuilder->from(from: Cluster::class, alias: 'cluster');
        $queryBuilder->leftJoin(join: 'cluster.projectsPrimary', alias: 'cluster_entity_project');
        $queryBuilder->leftJoin(
            join: 'cluster_entity_project.partners',
            alias: 'cluster_project_primary_partner'
        );
        $queryBuilder->groupBy(groupBy: 'cluster');
        $queryBuilder->orderBy(sort: 'cluster.name', order: Criteria::ASC);

        $primaryClusters = $queryBuilder->getQuery()->getArrayResult();

        // select secondary
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(
            'cluster.name',
            $queryBuilder->expr()->count(x: 'cluster_project_secondary_partner.id')
        );

        $queryBuilder->from(from: Cluster::class, alias: 'cluster');
        $queryBuilder->leftJoin(join: 'cluster.projectsSecondary', alias: 'cluster_entity_project');
        $queryBuilder->leftJoin(
            join: 'cluster_entity_project.partners',
            alias: 'cluster_project_secondary_partner'
        );
        $queryBuilder->groupBy(groupBy: 'cluster');
        $queryBuilder->orderBy(sort: 'cluster.name', order: Criteria::ASC);

        $secondaryClusters = $queryBuilder->getQuery()->getArrayResult();

        return array_map(static fn (array $cluster1, $cluster2) => [
            'name' => $cluster1['name'],
            '1'    => $cluster1[1],
            '2'    => $cluster2[1],
        ], $primaryClusters, $secondaryClusters);
    }

    public function fetchProgrammeCalls(User $user, $filter): array
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
        $queryBuilder->orderBy('cluster_entity_project.programmeCall', Criteria::ASC);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchProjectStatuses(User $user, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'project_status.status',
            $queryBuilder->expr()->count(x: 'project_status_project_partners.id')
        );

        $queryBuilder->from(from: Status::class, alias: 'project_status');
        $queryBuilder->join(join: 'project_status.projects', alias: 'project_status_project');
        $queryBuilder->join(
            join: 'project_status_project.partners',
            alias: 'project_status_project_partners'
        );

        //Join on partner to have the funder filter
        $queryBuilder->join(join: 'project_status_project_partners.project', alias: 'cluster_entity_project');
        $this->applyUserFilter(queryBuilder: $queryBuilder, user: $user);

        $queryBuilder->groupBy(groupBy: 'project_status');

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
