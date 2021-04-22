<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Repository\Statistics;

use Cluster\Entity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Program\Entity\Funder;

class PartnerRepository extends EntityRepository
{
    public function deletePartnersByInternalIdentifier(string $internalIdentifier): void
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->delete(Entity\Statistics\Partner::class, 'cluster_entity_statistics_partner');
        $queryBuilder->where('cluster_entity_statistics_partner.identifier = :internalIdentifier');
        $queryBuilder->setParameter('internalIdentifier', $internalIdentifier);

        $queryBuilder->getQuery()->execute();
    }

    public function getResults(Funder $funder, array $filter, int $result): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cluster_entity_statistics_partner');
        $queryBuilder->from(Entity\Statistics\Partner::class, 'cluster_entity_statistics_partner');

        if ($result === Entity\Statistics\Partner::RESULT_PROJECT) {
            $queryBuilder->addGroupBy('cluster_entity_statistics_partner.identifier');
        }

        if ($result === Entity\Statistics\Partner::RESULT_PARTNER) {
            $queryBuilder->addGroupBy('cluster_entity_statistics_partner.partnerIdentifier');
            $filter['country_method'] = 'or';
        }


        $this->applyFilters($filter, $queryBuilder);
        $this->applyFunderFilter($queryBuilder, $funder);

      //  print $queryBuilder->getQuery()->getSQL(); die();

        return $queryBuilder->getQuery()->getArrayResult();
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
                        ->select('cluster_entity_statistics_partner_country_filter.identifier')
                        ->from(Entity\Statistics\Partner::class, 'cluster_entity_statistics_partner_country_filter')
                        ->where($queryBuilder->expr()->in('cluster_entity_statistics_partner_country_filter.country', $countryFilter))
                        ->addGroupBy('cluster_entity_statistics_partner_country_filter.identifier')
                        ->having('COUNT(DISTINCT cluster_entity_statistics_partner_country_filter.country) > ' . (count($countryFilter) - 1));

                    $queryBuilder->andWhere($queryBuilder->expr()->in('cluster_entity_statistics_partner.identifier', $countryFilterSubSelect->getDQL()));

                    break;
                case 'or':
                    $queryBuilder->andWhere($queryBuilder->expr()->in('cluster_entity_statistics_partner.country', $countryFilter));
                    break;
            }
        }

        $partnerTypeFilter = $filter['partner_type'] ?? [];
        if (!empty($partnerTypeFilter)) {
            switch ($filter['partner_type_method']) {
                case 'and':
                    //Find the projects where the country is active
                    $partnerTypeSubSelect = $this->_em->createQueryBuilder()
                        ->select('cluster_entity_statistics_partner_partner_type_filter.identifier')
                        ->from(Entity\Statistics\Partner::class, 'cluster_entity_statistics_partner_partner_type_filter')
                        ->where($queryBuilder->expr()->in('cluster_entity_statistics_partner_partner_type_filter.partnerType', $partnerTypeFilter))
                        ->addGroupBy('cluster_entity_statistics_partner_partner_type_filter.identifier')
                        ->having('COUNT(DISTINCT cluster_entity_statistics_partner_partner_type_filter.partnerType) > ' . (count($partnerTypeFilter) - 1));

                    $queryBuilder->andWhere($queryBuilder->expr()->in('cluster_entity_statistics_partner.identifier', $partnerTypeSubSelect->getDQL()));

                    break;
                case 'or':
                    $queryBuilder->andWhere($queryBuilder->expr()->in('cluster_entity_statistics_partner.partnerType', $partnerTypeFilter));
                    break;
            }
        }

        $projectStatusFilter = $filter['project_status'] ?? [];
        if (!empty($projectStatusFilter)) {
            switch ($filter['project_status_method']) {
                case 'and':
                    //Find the projects where the country is active
                    $projectStatusSubSelect = $this->_em->createQueryBuilder()
                        ->select('cluster_entity_statistics_partner_project_status_filter.identifier')
                        ->from(Entity\Statistics\Partner::class, 'cluster_entity_statistics_partner_project_status_filter')
                        ->where($queryBuilder->expr()->in('cluster_entity_statistics_partner_project_status_filter.projectStatus', $projectStatusFilter))
                        ->addGroupBy('cluster_entity_statistics_partner_project_status_filter.identifier')
                        ->having('COUNT(DISTINCT cluster_entity_statistics_partner_project_status_filter.projectStatus) > ' . (count($partnerTypeFilter) - 1));

                    $queryBuilder->andWhere($queryBuilder->expr()->in('cluster_entity_statistics_partner.identifier', $projectStatusSubSelect->getDQL()));

                    break;
                case 'or':
                    $queryBuilder->andWhere($queryBuilder->expr()->in('cluster_entity_statistics_partner.projectStatus', $projectStatusFilter));
                    break;
            }
        }

        $primaryClusterFilter = $filter['primary_cluster'] ?? [];
        if (!empty($primaryClusterFilter)) {
            switch ($filter['primary_cluster_method']) {
                case 'and':
                    //Find the projects where the country is active
                    $primaryClusterSubSelect = $this->_em->createQueryBuilder()
                        ->select('cluster_entity_statistics_partner_primary_cluster_filter.identifier')
                        ->from(Entity\Statistics\Partner::class, 'cluster_entity_statistics_partner_primary_cluster_filter')
                        ->where($queryBuilder->expr()->in('cluster_entity_statistics_partner_primary_cluster_filter.primaryCluster', $primaryClusterFilter))
                        ->addGroupBy('cluster_entity_statistics_partner_primary_cluster_filter.identifier')
                        ->having('COUNT(DISTINCT cluster_entity_statistics_partner_primary_cluster_filter.primaryCluster) > ' . (count($partnerTypeFilter) - 1));

                    $queryBuilder->andWhere($queryBuilder->expr()->in('cluster_entity_statistics_partner.identifier', $primaryClusterSubSelect->getDQL()));

                    break;
                case 'or':
                    $queryBuilder->andWhere($queryBuilder->expr()->in('cluster_entity_statistics_partner.primaryCluster', $primaryClusterFilter));
                    break;
            }
        }

        $yearFilter = $filter['year'] ?? [];
        if (!empty($yearFilter)) {
            $queryBuilder->andWhere($queryBuilder->expr()->in('cluster_entity_statistics_partner.year', $yearFilter));
        }
    }

    private function applyFunderFilter(QueryBuilder $queryBuilder, Funder $funder): void
    {
        //Find the projects where the country is active
        $funderSubSelect = $this->_em->createQueryBuilder()
            ->select('cluster_entity_statistics_partner_country.identifier')
            ->from(Entity\Statistics\Partner::class, 'cluster_entity_statistics_partner_country')
            ->andWhere('cluster_entity_statistics_partner_country.country = :funder_country');

        $queryBuilder->andWhere($queryBuilder->expr()->in('cluster_entity_statistics_partner.identifier', $funderSubSelect->getDQL()));
        $queryBuilder->setParameter('funder_country', $funder->getCountry()->getCd());
    }

    public function fetchCountries(Funder $funder, $filter, int $result): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        if ($result === Entity\Statistics\Partner::RESULT_PROJECT) {
            $queryBuilder->select(
                'cluster_entity_statistics_partner.country',
                $queryBuilder->expr()->countDistinct('cluster_entity_statistics_partner.identifier')
            );
        }

        if ($result === Entity\Statistics\Partner::RESULT_PARTNER) {
            $queryBuilder->select(
                'cluster_entity_statistics_partner.country',
                $queryBuilder->expr()->countDistinct('cluster_entity_statistics_partner.partnerIdentifier')
            );
        }


        $queryBuilder->from(Entity\Statistics\Partner::class, 'cluster_entity_statistics_partner');
        $queryBuilder->groupBy('cluster_entity_statistics_partner.country');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchPartnerTypes(Funder $funder, $filter, int $result): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        if ($result === Entity\Statistics\Partner::RESULT_PROJECT) {
            $queryBuilder->select(
                'cluster_entity_statistics_partner.partnerType',
                $queryBuilder->expr()->countDistinct('cluster_entity_statistics_partner.identifier')
            );
        }

        if ($result === Entity\Statistics\Partner::RESULT_PARTNER) {
            $queryBuilder->select(
                'cluster_entity_statistics_partner.partnerType',
                $queryBuilder->expr()->countDistinct('cluster_entity_statistics_partner.partnerIdentifier')
            );
        }

        $queryBuilder->from(Entity\Statistics\Partner::class, 'cluster_entity_statistics_partner');
        $queryBuilder->groupBy('cluster_entity_statistics_partner.partnerType');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchPrimaryClusters(Funder $funder, $filter, int $result): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        if ($result === Entity\Statistics\Partner::RESULT_PROJECT) {
            $queryBuilder->select(
                'cluster_entity_statistics_partner.primaryCluster',
                $queryBuilder->expr()->countDistinct('cluster_entity_statistics_partner.identifier')
            );
        }

        if ($result === Entity\Statistics\Partner::RESULT_PARTNER) {
            $queryBuilder->select(
                'cluster_entity_statistics_partner.primaryCluster',
                $queryBuilder->expr()->countDistinct('cluster_entity_statistics_partner.primaryCluster')
            );
        }

        $queryBuilder->from(Entity\Statistics\Partner::class, 'cluster_entity_statistics_partner');
        $queryBuilder->groupBy('cluster_entity_statistics_partner.primaryCluster');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchProjectStatuses(Funder $funder, $filter, int $result): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        if ($result === Entity\Statistics\Partner::RESULT_PROJECT) {
            $queryBuilder->select(
                'cluster_entity_statistics_partner.projectStatus',
                $queryBuilder->expr()->countDistinct('cluster_entity_statistics_partner.identifier')
            );
        }

        if ($result === Entity\Statistics\Partner::RESULT_PARTNER) {
            $queryBuilder->select(
                'cluster_entity_statistics_partner.projectStatus',
                $queryBuilder->expr()->countDistinct('cluster_entity_statistics_partner.projectStatus')
            );
        }

        $queryBuilder->from(Entity\Statistics\Partner::class, 'cluster_entity_statistics_partner');
        $queryBuilder->groupBy('cluster_entity_statistics_partner.projectStatus');

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function fetchYears(Funder $funder): array
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('cluster_entity_statistics_partner.year')
            ->distinct(true)
            ->from(Entity\Statistics\Partner::class, 'cluster_entity_statistics_partner')
            ->orderBy('cluster_entity_statistics_partner.year', Criteria::ASC);

        $this->applyFunderFilter($queryBuilder, $funder);

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
