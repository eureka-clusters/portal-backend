<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Repository\Project;

use Cluster\Entity;
use Cluster\Entity\Funder;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 *
 */
class PartnerRepository extends EntityRepository
{
    public function getPartnersByFunderAndFilter(Entity\Funder $funder, array $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('project_partner');
        $queryBuilder->from(Entity\Project\Partner::class, 'project_partner');

        $this->applyFilters($filter, $queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }

    private function applyFilters(array $filter, QueryBuilder $queryBuilder): void
    {
        //Filters filters filters
        $countryFilter = $filter['country'] ?? [];

        if (!empty($countryFilter)) {
            switch ($filter['country_method']) {
                case 'and':
                    //Find the projects where the country is active
                    $countryFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('project_partner_filter_country')
                        ->from(Entity\Project\Partner::class, 'project_partner_filter_country')
                        ->join(
                            'project_partner_filter_country.organisation',
                            'project_partner_filter_country_organisation'
                        )
                        ->join('project_partner_filter_country_organisation.country', 'project_partner_filter_country_organisation_country')
                        ->where(
                            $queryBuilder->expr()->in(
                                'project_partner_filter_country_organisation_country.country',
                                $countryFilter
                            )
                        )
                        ->addGroupBy('project_partner_filter_country')
                        ->having(
                            'COUNT(DISTINCT project_partner_filter_country) > ' . (count(
                                    $countryFilter
                                ) - 1)
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('project_partner', $countryFilterSubSelect->getDQL())
                    );

                    break;
                case 'or':

                    //Find the projects where the country is active
                    $countryFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('project_partner_filter_country')
                        ->from(Entity\Project\Partner::class, 'project_partner_filter_country')
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

                    break;
            }
        }

        $organisationTypeFilter = $filter['organisation_type'] ?? [];

        if (!empty($organisationTypeFilter)) {
            switch ($filter['organisation_type_method']) {
                case 'and':
                    //Find the projects we have at least organisations with this type
                    $organisationTypeFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('project_partner_filter_organisation_type')
                        ->from(Entity\Project\Partner::class, 'project_partner_filter_organisation_type')
                        ->join(
                            'project_partner_filter_organisation_type.organisation',
                            'project_partner_filter_organisation_type_organisation'
                        )
                        ->join('project_partner_filter_organisation_type_organisation.type', 'project_partner_filter_organisation_type_organisation_type')
                        ->where(
                            $queryBuilder->expr()->in(
                                'project_partner_filter_organisation_type_organisation_type.type',
                                $organisationTypeFilter
                            )
                        )
                        ->addGroupBy('project_partner_filter_organisation_type_organisation_type')
                        ->having(
                            'COUNT(DISTINCT project_partner_filter_organisation_type_organisation_type) > ' . (count(
                                    $organisationTypeFilter
                                ) - 1)
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('project_partner', $organisationTypeFilterSubSelect->getDQL())
                    );

                    break;
                case 'or':

                    //Find the projects where we have organisations with this type
                    $organisationTypeFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('project_partner_filter_organisation_type')
                        ->from(Entity\Project\Partner::class, 'project_partner_filter_organisation_type')
                        ->join(
                            'project_partner_filter_organisation_type.organisation',
                            'project_partner_filter_organisation_type_organisation'
                        )
                        ->join('project_partner_filter_organisation_type_organisation.type', 'project_partner_filter_organisation_type_organisation_type')
                        ->where(
                            $queryBuilder->expr()->in(
                                'project_partner_filter_organisation_type_organisation_type.type',
                                $organisationTypeFilter
                            )
                        );


                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('project_partner', $organisationTypeFilterSubSelect->getDQL())
                    );

                    break;
            }
        }

        $projectStatusFilter = $filter['project_status'] ?? [];

        if (!empty($projectStatusFilter)) {
            switch ($filter['project_status_method']) {
                case 'and':
                    //Find the projects we have at least organisations with this type
                    $projectStatusFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('project_partner_filter_project_status')
                        ->from(Entity\Project\Partner::class, 'project_partner_filter_project_status')
                        ->join('project_partner_filter_project_status.project', 'project_partner_filter_project_status_project')
                        ->join('project_partner_filter_project_status_project.status', 'project_partner_filter_project_status_project_status')
                        ->where(
                            $queryBuilder->expr()->in(
                                'project_partner_filter_project_status_project_status.status',
                                $projectStatusFilter
                            )
                        )
                        ->addGroupBy('project_partner_filter_project_status_project_status')
                        ->having(
                            'COUNT(DISTINCT project_partner_filter_project_status_project_status) > ' . (count(
                                    $projectStatusFilter
                                ) - 1)
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('project_partner', $projectStatusFilterSubSelect->getDQL())
                    );

                    break;
                case 'or':

                    //Find the projects where we have organisations with this type
                    $projectStatusFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('project_partner_filter_project_status')
                        ->from(Entity\Project\Partner::class, 'project_partner_filter_project_status')
                        ->join('project_partner_filter_project_status.project', 'project_partner_filter_project_status_project')
                        ->join('project_partner_filter_project_status_project.status', 'project_partner_filter_project_status_project_status')
                        ->where(
                            $queryBuilder->expr()->in(
                                'project_partner_filter_project_status_project_status.status',
                                $projectStatusFilter
                            )
                        );


                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('project_partner', $projectStatusFilterSubSelect->getDQL())
                    );

                    break;
            }
        }


        $primaryClusterFilter = $filter['primary_cluster'] ?? [];

        if (!empty($primaryClusterFilter)) {
            switch ($filter['primary_cluster_method']) {
                case 'and':
                    //Find the projects we have at least organisations with this type
                    $primaryClusterFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('project_partner_filter_primary_cluster')
                        ->from(Entity\Project\Partner::class, 'project_partner_filter_primary_cluster')
                        ->join('project_partner_filter_primary_cluster.project', 'project_partner_filter_primary_cluster_project')
                        ->join('project_partner_filter_primary_cluster_project.primaryCluster', 'project_partner_filter_primary_cluster_project_primary_cluster')
                        ->where(
                            $queryBuilder->expr()->in(
                                'project_partner_filter_primary_cluster_project_primary_cluster.name',
                                $primaryClusterFilter
                            )
                        )
                        ->addGroupBy('project_partner_filter_primary_cluster_project_primary_cluster')
                        ->having(
                            'COUNT(DISTINCT project_partner_filter_primary_cluster_project_primary_cluster) > ' . (count(
                                    $primaryClusterFilter
                                ) - 1)
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('project_partner', $primaryClusterFilterSubSelect->getDQL())
                    );

                    break;
                case 'or':

                    //Find the projects where we have organisations with this type
                    $primaryClusterFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('project_partner_filter_primary_cluster')
                        ->from(Entity\Project\Partner::class, 'project_partner_filter_primary_cluster')
                        ->join('project_partner_filter_primary_cluster.project', 'project_partner_filter_primary_cluster_project')
                        ->join('project_partner_filter_primary_cluster_project.primaryCluster', 'project_partner_filter_primary_cluster_project_primary_cluster')
                        ->where(
                            $queryBuilder->expr()->in(
                                'project_partner_filter_primary_cluster_project_primary_cluster.name',
                                $primaryClusterFilter
                            )
                        );


                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('project_partner', $primaryClusterFilterSubSelect->getDQL())
                    );

                    break;
            }
        }
    }

    public function getPartnersByProject(Entity\Project $project): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('project_partner');
        $queryBuilder->from(Entity\Project\Partner::class, 'project_partner');
        $queryBuilder->join('project_partner.organisation', 'organisation');
        $queryBuilder->where('project_partner.project = :project');
        $queryBuilder->setParameter('project', $project);
        $queryBuilder->addOrderBy('organisation.name');

        return $queryBuilder->getQuery()->getResult();
    }

    public function fetchCountries(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'country.country',
            $queryBuilder->expr()->count('project_partner.id')
        );

        $queryBuilder->from(Entity\Project\Partner::class, 'project_partner');
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

        $queryBuilder->from(Entity\Organisation\Type::class, 'organisation_type');
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

        $queryBuilder->from(Entity\Cluster::class, 'cluster');
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

        $queryBuilder->from(Entity\Project\Status::class, 'project_status');
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
            ->from(Entity\Project\Version\CostsAndEffort::class, 'project_version_costs_and_effort')
            ->orderBy('project_version_costs_and_effort.year', Criteria::ASC);

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
