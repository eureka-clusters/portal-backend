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
    public function getPartnersByFunderAndFilter(Funder $funder, array $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('project_partner');
        $queryBuilder->from(Partner::class, 'project_partner');

        $this->applyFilters($filter, $queryBuilder);

        return $queryBuilder->getQuery()->getResult();
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

        $primaryClusterFilter = $filter['primary_cluster'] ?? [];

        if (!empty($primaryClusterFilter)) {
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
                        $primaryClusterFilter
                    )
                );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in('project_partner', $primaryClusterFilterSubSelect->getDQL())
            );
        }
    }

    public function getPartnersByProject(Project $project): QueryBuilder
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('project_partner');
        $queryBuilder->from(Partner::class, 'project_partner');
        $queryBuilder->join('project_partner.organisation', 'organisation');
        $queryBuilder->where('project_partner.project = :project');
        $queryBuilder->setParameter('project', $project);
        $queryBuilder->addOrderBy('organisation.name');

        return $queryBuilder;
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

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchPrimaryClusters(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster.name',
            $queryBuilder->expr()->count('cluster_project_primary_partner.id')
        );

        $queryBuilder->from(Cluster::class, 'cluster');
        $queryBuilder->join('cluster.projectsPrimary', 'cluster_project_primary');
        $queryBuilder->join(
            'cluster_project_primary.partners',
            'cluster_project_primary_partner'
        );

        $queryBuilder->groupBy('cluster');

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

        $queryBuilder->groupBy('project_status');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchYears(Funder $funder): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('project_version_costs_and_effort.year')
            ->distinct(true)
            ->from(CostsAndEffort::class, 'project_version_costs_and_effort')
            ->orderBy('project_version_costs_and_effort.year', Criteria::ASC);

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
