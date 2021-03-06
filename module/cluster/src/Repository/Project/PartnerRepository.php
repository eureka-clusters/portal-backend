<?php

declare(strict_types=1);

namespace Cluster\Repository\Project;

use Cluster\Entity\Cluster;
use Cluster\Entity\Funder;
use Cluster\Entity\Organisation;
use Cluster\Entity\Organisation\Type;
use Cluster\Entity\Project;
use Cluster\Entity\Project\Partner;
use Cluster\Entity\Project\Status;
use Cluster\Entity\Project\Version\CostsAndEffort;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class PartnerRepository extends EntityRepository
{
    public function getPartnersByFunderAndFilter(
        Funder $funder,
        array $filter,
        string $sort = 'partner.organisation.name',
        string $order = 'asc'
    ): QueryBuilder {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('project_partner');
        $queryBuilder->from(Partner::class, 'project_partner');

        //We always need a join on project
        $queryBuilder->join('project_partner.project', 'project');

        $this->applyFilters($filter, $queryBuilder);
        $this->applySorting($sort, $order, $queryBuilder);
        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder;
    }

    private function applyFilters(array $filter, QueryBuilder $queryBuilder): void
    {
        //Filters filters filters
        $countryFilter = $filter['country'] ?? [];

        if (!empty($countryFilter)) {
            //Find the projects where the country is active
            $countryFilterSubSelect = $this->_em->createQueryBuilder()
                ->select('project_partner_filter_country')
                ->from(Partner::class, 'project_partner_filter_country')
                ->join(
                    'project_partner_filter_country.organisation',
                    'project_partner_filter_country_organisation'
                )
                ->join('project_partner_filter_country_organisation.country', 'country')
                ->where(
                    $queryBuilder->expr()->in(
                        'country.country',
                        $countryFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('project_partner', $countryFilterSubSelect->getDQL())
            );
        }

        $organisationTypeFilter = $filter['organisation_type'] ?? [];

        if (!empty($organisationTypeFilter)) {
            //Find the projects where we have organisations with this type
            $organisationTypeFilterSubSelect = $this->_em->createQueryBuilder()
                ->select('project_partner_filter_organisation_type')
                ->from(Partner::class, 'project_partner_filter_organisation_type')
                ->join(
                    'project_partner_filter_organisation_type.organisation',
                    'project_partner_filter_organisation_type_organisation'
                )
                ->join(
                    'project_partner_filter_organisation_type_organisation.type',
                    'project_partner_filter_organisation_type_organisation_type'
                )
                ->where(
                    $queryBuilder->expr()->in(
                        'project_partner_filter_organisation_type_organisation_type.type',
                        $organisationTypeFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('project_partner', $organisationTypeFilterSubSelect->getDQL())
            );
        }

        $projectStatusFilter = $filter['project_status'] ?? [];

        if (!empty($projectStatusFilter)) {
            //Find the projects where we have organisations with this type
            $projectStatusFilterSubSelect = $this->_em->createQueryBuilder()
                ->select('project_partner_filter_project_status')
                ->from(Partner::class, 'project_partner_filter_project_status')
                ->join(
                    'project_partner_filter_project_status.project',
                    'project_partner_filter_project_status_project'
                )
                ->join(
                    'project_partner_filter_project_status_project.status',
                    'project_partner_filter_project_status_project_status'
                )
                ->where(
                    $queryBuilder->expr()->in(
                        'project_partner_filter_project_status_project_status.status',
                        $projectStatusFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('project_partner', $projectStatusFilterSubSelect->getDQL())
            );
        }

        $clustersFilter = $filter['clusters'] ?? [];

        if (!empty($clustersFilter)) {
            //Find the projects where we have organisations with this type
            $primaryClusterFilterSubSelect = $this->_em->createQueryBuilder()
                ->select('project_partner_filter_primary_cluster')
                ->from(Partner::class, 'project_partner_filter_primary_cluster')
                ->join(
                    'project_partner_filter_primary_cluster.project',
                    'project_partner_filter_primary_cluster_project'
                )
                ->join(
                    'project_partner_filter_primary_cluster_project.primaryCluster',
                    'project_partner_filter_primary_cluster_project_primary_cluster'
                )
                ->where(
                    $queryBuilder->expr()->in(
                        'project_partner_filter_primary_cluster_project_primary_cluster.name',
                        $clustersFilter
                    )
                );

            $secondaryClusterFilterSubSelect = $this->_em->createQueryBuilder()
                ->select('project_partner_filter_secondary_cluster')
                ->from(Partner::class, 'project_partner_filter_secondary_cluster')
                ->join(
                    'project_partner_filter_secondary_cluster.project',
                    'project_partner_filter_secondary_cluster_project'
                )
                ->join(
                    'project_partner_filter_secondary_cluster_project.secondaryCluster',
                    'project_partner_filter_secondary_cluster_project_secondary_cluster'
                )
                ->where(
                    $queryBuilder->expr()->in(
                        'project_partner_filter_secondary_cluster_project_secondary_cluster.name',
                        $clustersFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->in('project_partner', $primaryClusterFilterSubSelect->getDQL()),
                    $queryBuilder->expr()->in('project_partner', $secondaryClusterFilterSubSelect->getDQL()),
                )
            );
        }

        $programmeCallFilter = $filter['programme_call'] ?? [];

        if (!empty($programmeCallFilter)) {
            //Find the projects who are in the call
            $programmeCallFilterSubset = $this->_em->createQueryBuilder()
                ->select('project_partner_filter_programme_call')
                ->from(Partner::class, 'project_partner_filter_programme_call')
                ->join(
                    'project_partner_filter_programme_call.project',
                    'project_partner_filter_programme_call_project'
                )
                ->where(
                    $queryBuilder->expr()->in(
                        'project_partner_filter_programme_call_project.programmeCall',
                        $programmeCallFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('project_partner', $programmeCallFilterSubset->getDQL())
            );
        }

        $yearFilter = $filter['year'] ?? [];

        if (!empty($yearFilter)) {
            $queryBuilder->select('project_partner', 'project_partner_costs_and_effort');
            //If we have a year, then we join on costs and effort
            $queryBuilder->join('project_partner.costsAndEffort', 'project_partner_costs_and_effort');
            $queryBuilder->join('project_partner_costs_and_effort.version', 'project_partner_costs_and_effort_version');

            $queryBuilder->andWhere($queryBuilder->expr()->in('project_partner_costs_and_effort.year', $yearFilter));
            $queryBuilder->andWhere('project_partner_costs_and_effort_version.type = :type');
            $queryBuilder->setParameter('type', (new Type())->setId(3));
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
                $sortColumn = 'project.name';
                break;
            case 'partner.organisation.name':
                $sortColumn = 'organisation.name';
                $queryBuilder->join('project_partner.organisation', 'organisation');
                break;
            case 'partner.organisation.country.country':
                $sortColumn = 'organisation_country.country';
                $queryBuilder->join('project_partner.organisation', 'organisation');
                $queryBuilder->join('organisation.country', 'organisation_country');
                break;
            case 'partner.organisation.type.type':
                $sortColumn = 'organisation_type.type';
                $queryBuilder->join('project_partner.organisation', 'organisation');
                $queryBuilder->join('organisation.type', 'organisation_type');
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
            $queryBuilder->orderBy($sortColumn, $order);
        }
    }

    private function applyFunderFilter(QueryBuilder $queryBuilder, Funder $funder): void
    {
        //Find the projects where the country is active
        $funderSubSelect = $this->_em->createQueryBuilder()
            ->select('cluster_entity_project_funder')
            ->from(Project::class, 'cluster_entity_project_funder')
            ->join('cluster_entity_project_funder.partners', 'cluster_entity_project_funder_partners')
            ->join(
                'cluster_entity_project_funder_partners.organisation',
                'cluster_entity_project_funder_partners_organisation'
            )
            ->andWhere('cluster_entity_project_funder_partners_organisation.country = :funder_country');


        $queryBuilder->andWhere(
            $queryBuilder->expr()->in('project', $funderSubSelect->getDQL())
        );
        $queryBuilder->setParameter('funder_country', $funder->getCountry());
    }

    public function getPartnersByProject(Project $project): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('project_partner');
        $queryBuilder->from(Partner::class, 'project_partner');

        $this->activeInLatestVersionSubselect($queryBuilder, $project);

        $queryBuilder->join('project_partner.organisation', 'organisation');
        $queryBuilder->addOrderBy('organisation.name');


        return $queryBuilder;
    }

    private function activeInLatestVersionSubselect(QueryBuilder $queryBuilder, Project $project): void
    {
        $activeInLatestVersionSubSelect = $this->_em->createQueryBuilder();
        $activeInLatestVersionSubSelect->select('project_partner_subselect');
        $activeInLatestVersionSubSelect->from(Partner::class, 'project_partner_subselect');
        $activeInLatestVersionSubSelect->join('project_partner_subselect.project', 'project_partner_subselect_project');
        $activeInLatestVersionSubSelect->join('project_partner_subselect.costsAndEffort', 'project_partner_subselect_costs_and_effort');
        $activeInLatestVersionSubSelect->join(
            'project_partner_subselect_costs_and_effort.version',
            'project_partner_subselect_costs_and_effort_version'
        );
        $activeInLatestVersionSubSelect->join(
            'project_partner_subselect_costs_and_effort_version.type',
            'project_partner_subselect_costs_and_effort_version_type'
        );
        $activeInLatestVersionSubSelect->andWhere('project_partner_subselect_costs_and_effort_version_type.type = :type');
        $activeInLatestVersionSubSelect->andWhere('project_partner_subselect_project.id = :projectId');

        $queryBuilder->setParameter(key: 'type', value: \Cluster\Entity\Version\Type::TYPE_LATEST);
        $queryBuilder->setParameter('projectId', $project->getId());
        $queryBuilder->andWhere(
            $queryBuilder->expr()->in('project_partner', $activeInLatestVersionSubSelect->getDQL())
        );
    }

    public function getPartnersByOrganisation(Organisation $organisation): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('project_partner');
        $queryBuilder->from(Partner::class, 'project_partner');
        $queryBuilder->join('project_partner.organisation', 'project_partner_organisation');
        $queryBuilder->where('project_partner_organisation = :organisation');
        $queryBuilder->setParameter('organisation', $organisation);
        $queryBuilder->addOrderBy('project_partner_organisation.name');

        return $queryBuilder;
    }

    public function fetchCountries(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'country.country',
            $queryBuilder->expr()->count('project_partner.id')
        );

        $queryBuilder->from(Partner::class, 'project_partner');
        $queryBuilder->join('project_partner.organisation', 'organisation');
        $queryBuilder->join('organisation.country', 'country');
        $queryBuilder->groupBy('country');

        //Join on partner to have the funder filter
        $queryBuilder->join('project_partner.project', 'project');
        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchOrganisationTypes(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'organisation_type.type',
            $queryBuilder->expr()->count('organisation_partners.id')
        );

        $queryBuilder->from(Type::class, 'organisation_type');
        $queryBuilder->join('organisation_type.organisations', 'organisation');
        $queryBuilder->join('organisation.partners', 'organisation_partners');
        $queryBuilder->groupBy('organisation_type');

        //Join on partner to have the funder filter
        $queryBuilder->join('organisation_partners.project', 'project');
        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchClusters(Funder $funder, $filter): array
    {
        // it should be a left join so that all clusters are returned even with 0 projects
        $queryBuilder = $this->_em->createQueryBuilder();

        // select primary
        $queryBuilder->select(
            'cluster.name',
            $queryBuilder->expr()->count('cluster_project_primary_partner.id')
        );

        $queryBuilder->from(Cluster::class, 'cluster');
        $queryBuilder->leftJoin('cluster.projectsPrimary', 'project');
        $queryBuilder->leftJoin(
            'project.partners',
            'cluster_project_primary_partner'
        );
        $queryBuilder->groupBy('cluster');
        $queryBuilder->orderBy('cluster.name', Criteria::ASC);

        //Filters do not work here
        //$this->applyFunderFilter($queryBuilder, $funder);

        $primaryClusters = $queryBuilder->getQuery()->getArrayResult();


        // select secondary
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select(
            'cluster.name',
            $queryBuilder->expr()->count('cluster_project_secondary_partner.id')
        );

        $queryBuilder->from(Cluster::class, 'cluster');
        $queryBuilder->leftJoin('cluster.projectsSecondary', 'project');
        $queryBuilder->leftJoin(
            'project.partners',
            'cluster_project_secondary_partner'
        );
        $queryBuilder->groupBy('cluster');
        $queryBuilder->orderBy('cluster.name', Criteria::ASC);

        //Join on partner to have the funder filter
        //        $queryBuilder->join('cluster_project_secondary_partner.project', 'project');
        //   $this->applyFunderFilter($queryBuilder, $funder);

        $secondaryClusters = $queryBuilder->getQuery()->getArrayResult();

        return array_map(static fn(array $cluster1, $cluster2) => [
            'name' => $cluster1['name'],
            '1'    => $cluster1[1],
            '2'    => $cluster2[1],
        ], $primaryClusters, $secondaryClusters);
    }

    public function fetchProgrammeCalls(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'project.programmeCall',
            $queryBuilder->expr()->count('project_partners.id')
        );

        $queryBuilder->from(Project::class, 'project');
        $queryBuilder->join(
            'project.partners',
            'project_partners'
        );

        $this->applyFunderFilter($queryBuilder, $funder);

        $queryBuilder->groupBy('project.programmeCall');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchProjectStatuses(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'project_status.status',
            $queryBuilder->expr()->count('project_status_project_partners.id')
        );

        $queryBuilder->from(Status::class, 'project_status');
        $queryBuilder->join('project_status.projects', 'project_status_project');
        $queryBuilder->join(
            'project_status_project.partners',
            'project_status_project_partners'
        );

        //Join on partner to have the funder filter
        $queryBuilder->join('project_status_project_partners.project', 'project');
        $this->applyFunderFilter($queryBuilder, $funder);

        $queryBuilder->groupBy('project_status');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchYears(): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('project_version_costs_and_effort.year')
            ->distinct(true)
            ->from(CostsAndEffort::class, 'project_version_costs_and_effort')
            ->orderBy('project_version_costs_and_effort.year', Criteria::ASC);

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
