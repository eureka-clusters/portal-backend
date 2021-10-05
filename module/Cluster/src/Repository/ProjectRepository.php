<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Repository;

use Cluster\Entity;
use Cluster\Entity\Funder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 *
 */
class ProjectRepository extends EntityRepository
{
    public function getProjectsByFunderAndFilter(Entity\Funder $funder, array $filter)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_project');
        $queryBuilder->from(Entity\Project::class, 'cluster_entity_project');

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
                        ->select('cluster_entity_project_partner.project')
                        ->from(Entity\Project\Partner::class, 'cluster_entity_project_partner')
                        ->join(
                            'cluster_entity_project_partner.organisation',
                            'cluster_entity_project_partner_organisation'
                        )
                        ->where(
                            $queryBuilder->expr()->in(
                                'cluster_entity_project_partner_organisation.country',
                                $countryFilter
                            )
                        )
                        ->addGroupBy('cluster_entity_project_partner.project')
                        ->having(
                            'COUNT(DISTINCT cluster_entity_project_partner_organisation.country) > ' . (count(
                                    $countryFilter
                                ) - 1)
                        );

                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('cluster_entity_project', $countryFilterSubSelect->getDQL())
                    );

                    break;
                case 'or':

                    //Find the projects where the country is active
                    $countryFilterSubSelect = $this->_em->createQueryBuilder()
                        ->select('cluster_entity_project_partner.project')
                        ->from(Entity\Project\Partner::class, 'cluster_entity_project_partner')
                        ->join(
                            'cluster_entity_project_partner.organisation',
                            'cluster_entity_project_partner_organisation'
                        )
                        ->where(
                            $queryBuilder->expr()->in(
                                'cluster_entity_project_partner_organisation.country',
                                $countryFilter
                            )
                        );


                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->in('cluster_entity_project', $countryFilterSubSelect->getDQL())
                    );

                    break;
            }
        }

//        $partnerTypeFilter = $filter['partner_type'] ?? [];
//        if (!empty($partnerTypeFilter)) {
//            switch ($filter['partner_type_method']) {
//                case 'and':
//                    //Find the projects where the country is active
//                    $partnerTypeSubSelect = $this->_em->createQueryBuilder()
//                        ->select('cluster_entity_statistics_partner_partner_type_filter.identifier')
//                        ->from(
//                            Entity\Statistics\Partner::class,
//                            'cluster_entity_statistics_partner_partner_type_filter'
//                        )
//                        ->where(
//                            $queryBuilder->expr()->in(
//                                'cluster_entity_statistics_partner_partner_type_filter.partnerType',
//                                $partnerTypeFilter
//                            )
//                        )
//                        ->addGroupBy('cluster_entity_statistics_partner_partner_type_filter.identifier')
//                        ->having(
//                            'COUNT(DISTINCT cluster_entity_statistics_partner_partner_type_filter.partnerType) > ' . (count(
//                                    $partnerTypeFilter
//                                ) - 1)
//                        );
//
//                    $queryBuilder->andWhere(
//                        $queryBuilder->expr()->in(
//                            'cluster_entity_statistics_partner.identifier',
//                            $partnerTypeSubSelect->getDQL()
//                        )
//                    );
//
//                    break;
//                case 'or':
//                    $queryBuilder->andWhere(
//                        $queryBuilder->expr()->in('cluster_entity_statistics_partner.partnerType', $partnerTypeFilter)
//                    );
//                    break;
//            }
//        }
//
//        $projectStatusFilter = $filter['project_status'] ?? [];
//        if (!empty($projectStatusFilter)) {
//            switch ($filter['project_status_method']) {
//                case 'and':
//                    //Find the projects where the country is active
//                    $projectStatusSubSelect = $this->_em->createQueryBuilder()
//                        ->select('cluster_entity_statistics_partner_project_status_filter.identifier')
//                        ->from(
//                            Entity\Statistics\Partner::class,
//                            'cluster_entity_statistics_partner_project_status_filter'
//                        )
//                        ->where(
//                            $queryBuilder->expr()->in(
//                                'cluster_entity_statistics_partner_project_status_filter.projectStatus',
//                                $projectStatusFilter
//                            )
//                        )
//                        ->addGroupBy('cluster_entity_statistics_partner_project_status_filter.identifier')
//                        ->having(
//                            'COUNT(DISTINCT cluster_entity_statistics_partner_project_status_filter.projectStatus) > ' . (count(
//                                    $partnerTypeFilter
//                                ) - 1)
//                        );
//
//                    $queryBuilder->andWhere(
//                        $queryBuilder->expr()->in(
//                            'cluster_entity_statistics_partner.identifier',
//                            $projectStatusSubSelect->getDQL()
//                        )
//                    );
//
//                    break;
//                case 'or':
//                    $queryBuilder->andWhere(
//                        $queryBuilder->expr()->in(
//                            'cluster_entity_statistics_partner.projectStatus',
//                            $projectStatusFilter
//                        )
//                    );
//                    break;
//            }
//        }
//
//        $primaryClusterFilter = $filter['primary_cluster'] ?? [];
//        if (!empty($primaryClusterFilter)) {
//            switch ($filter['primary_cluster_method']) {
//                case 'and':
//                    //Find the projects where the country is active
//                    $primaryClusterSubSelect = $this->_em->createQueryBuilder()
//                        ->select('cluster_entity_statistics_partner_primary_cluster_filter.identifier')
//                        ->from(
//                            Entity\Statistics\Partner::class,
//                            'cluster_entity_statistics_partner_primary_cluster_filter'
//                        )
//                        ->where(
//                            $queryBuilder->expr()->in(
//                                'cluster_entity_statistics_partner_primary_cluster_filter.primaryCluster',
//                                $primaryClusterFilter
//                            )
//                        )
//                        ->addGroupBy('cluster_entity_statistics_partner_primary_cluster_filter.identifier')
//                        ->having(
//                            'COUNT(DISTINCT cluster_entity_statistics_partner_primary_cluster_filter.primaryCluster) > ' . (count(
//                                    $partnerTypeFilter
//                                ) - 1)
//                        );
//
//                    $queryBuilder->andWhere(
//                        $queryBuilder->expr()->in(
//                            'cluster_entity_statistics_partner.identifier',
//                            $primaryClusterSubSelect->getDQL()
//                        )
//                    );
//
//                    break;
//                case 'or':
//                    $queryBuilder->andWhere(
//                        $queryBuilder->expr()->in(
//                            'cluster_entity_statistics_partner.primaryCluster',
//                            $primaryClusterFilter
//                        )
//                    );
//                    break;
//            }
//        }
    }

    public function fetchOrganisationTypes(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_organisation_type.type',
            $queryBuilder->expr()->countDistinct('cluster_entity_project')
        );

        $queryBuilder->from(Entity\Organisation\Type::class, 'cluster_entity_organisation_type')
            ->join('cluster_entity_organisation_type.organisations', 'cluster_entity_organisation_type_organisations')
            ->join(
                'cluster_entity_organisation_type_organisations.partners',
                'cluster_entity_organisation_type_organisations_partners'
            )
            ->join('cluster_entity_organisation_type_organisations_partners.project', 'cluster_entity_project')
            ->groupBy('cluster_entity_organisation_type');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    private function applyFunderFilter(QueryBuilder $queryBuilder, Funder $funder): void
    {
        return;

        //Find the projects where the country is active
        $funderSubSelect = $this->_em->createQueryBuilder()
            ->select('cluster_entity_project_funder')
            ->from(Entity\Project::class, 'cluster_entity_project_funder')
            ->join('cluster_entity_project_funder.partners', 'cluster_entity_project_funder_partners')
            ->join(
                'cluster_entity_project_funder_partners.organisation',
                'cluster_entity_project_funder_partners_organisation'
            )
            ->andWhere('cluster_entity_project_funder_partners_organisation.country = :funder_country');

        $queryBuilder->andWhere(
            $queryBuilder->expr()->in('cluster_entity_project', $funderSubSelect->getDQL())
        );
        $queryBuilder->setParameter('funder_country', $funder->getCountry()->getCd());
    }

    public function fetchCountries(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project_partners_organisation_country.country',
            $queryBuilder->expr()->countDistinct('cluster_entity_project.id')
        );

        $queryBuilder->from(Entity\Project::class, 'cluster_entity_project')
            ->join('cluster_entity_project.partners', 'cluster_entity_project_partners')
            ->join(
                'cluster_entity_project_partners.organisation',
                'cluster_entity_project_partners_organisation'
            )
            ->join(
                'cluster_entity_project_partners_organisation.country',
                'cluster_entity_project_partners_organisation_country'
            )
            ->groupBy('cluster_entity_project_partners_organisation.country');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchPrimaryClusters(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_cluster.name',
            $queryBuilder->expr()->count('cluster_entity_cluster_project_primary.id')
        );

        $queryBuilder->from(Entity\Cluster::class, 'cluster_entity_cluster')
            ->join('cluster_entity_cluster.projectsPrimary', 'cluster_entity_cluster_project_primary')
            ->groupBy('cluster_entity_cluster');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchProjectStatuses(Funder $funder, $filter): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select(
            'cluster_entity_project_status.status',
            $queryBuilder->expr()->count('cluster_entity_project_status_project.id')
        );

        $queryBuilder->from(Entity\Project\Status::class, 'cluster_entity_project_status')
            ->join('cluster_entity_project_status.projects', 'cluster_entity_project_status_project')
            ->groupBy('cluster_entity_project_status');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
