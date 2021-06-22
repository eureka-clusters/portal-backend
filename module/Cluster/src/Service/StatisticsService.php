<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Service;

use Application\Service\AbstractService;
use Cluster\Entity\Statistics\Partner;
use Cluster\Entity\Funder;

/**
 * Class ClusterService
 * @package Cluster\Service
 */
class StatisticsService extends AbstractService
{
    public function deletePartnersByInternalIdentifier(string $internalIdentifier): void
    {
        $this->entityManager->getRepository(Partner::class)->deletePartnersByInternalIdentifier($internalIdentifier);
    }

    public function getResults(Funder $funder, array $filter, int $output = Partner::RESULT_PROJECT): array
    {
        //Make the chart/partners the same
        if ($output === 3) {
            $output = 1;
        }

        return $this->entityManager->getRepository(Partner::class)->getResults($funder, $filter, $output);
    }

    public function generateFacets(Funder $funder, array $filter, int $output = Partner::RESULT_PROJECT): array
    {
        //Make the chart/partners the same
        if ($output === 3) {
            $output = 1;
        }

        $countries       = $this->entityManager->getRepository(Partner::class)->fetchCountries($funder, $filter, $output);
        $partnerTypes    = $this->entityManager->getRepository(Partner::class)->fetchPartnerTypes($funder, $filter, $output);
        $primaryClusters = $this->entityManager->getRepository(Partner::class)->fetchPrimaryClusters($funder, $filter, $output);
        $projectStatuses = $this->entityManager->getRepository(Partner::class)->fetchProjectStatuses($funder, $filter, $output);
        $years           = $this->entityManager->getRepository(Partner::class)->fetchYears($funder);

        $countriesIndexed = array_map(static function (array $country) {
            return [
                'country' => $country['country'],
                'amount'  => $country[1]
            ];
        }, $countries);

        $partnerTypesIndexed = array_map(static function (array $partnerType) {
            return [
                'partnerType' => $partnerType['partnerType'],
                'amount'      => $partnerType[1]
            ];
        }, $partnerTypes);

        $primaryClustersIndexed = array_map(static function (array $primaryCluster) {
            return [
                'primaryCluster' => $primaryCluster['primaryCluster'],
                'amount'         => $primaryCluster[1]
            ];
        }, $primaryClusters);

        $projectStatusIndexed = array_map(static function (array $projectStatus) {
            return [
                'projectStatus' => $projectStatus['projectStatus'],
                'amount'        => $projectStatus[1]
            ];
        }, $projectStatuses);

        return [
            'countries'          => $countriesIndexed,
            'organisation_types' => $partnerTypesIndexed,
            'project_status'     => $projectStatusIndexed,
            'primary_clusters'   => $primaryClustersIndexed,
            'years'              => $years,
        ];
    }
}
