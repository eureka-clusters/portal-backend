<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Provider;

use Cluster\Entity;
use Cluster\Service\PartnerService;
use Doctrine\Common\Cache\RedisCache;

/**
 * Class ProjectProvider
 * @package Project\Provider
 */
class PartnerProvider
{
    private RedisCache     $redisCache;
    private PartnerService $partnerService;

    public function __construct(RedisCache $redisCache, PartnerService $partnerService)
    {
        $this->redisCache     = $redisCache;
        $this->partnerService = $partnerService;
    }

    public function generateArray(Entity\Statistics\Partner $partner): array
    {
        $cacheKey = 'test';

        //$partnerData = false;//$this->redisCache->fetch($cacheKey);
        $partnerData = [
            'number'         => $partner->projectNumber,
            'projectName'           => $partner->projectName,
            'projectIdentifier'           => $partner->identifier,

            'title'          => $partner->projectTitle,
            'description'    => $partner->projectDescription,
            'technicalArea'  => $partner->technicalArea,
            'programme'      => $partner->programme,
            'programmeCall'  => $partner->programmeCall,
            'primaryCluster' => $partner->primaryCluster,
            'labelDate'      => $partner->labelDate->format(\DateTimeInterface::ATOM),
            'status'         => $partner->latestVersionStatus, //  Notice: Undefined property: Cluster\Entity\Statistics\Partner::$status



            'country'       => $partner->country,
            'partner'       => $partner->partner,
            'country'       => $partner->country,
            'partnerType'       => $partner->partnerType,
            'active'       => $partner->active,
            'coordinator'       => $partner->coordinator,
            'selfFunded'       => $partner->selfFunded,
            'technicalContact'       => $partner->technicalContact,
            'year'       => $partner->year,
            'poSubmissionDate'       => $partner->poSubmissionDate,
            'poStatus'       => $partner->poStatus,
            'poTotalEffort'       => $partner->poTotalEffort,
            'poTotalCosts'       => $partner->poTotalCosts,
            'poEffort'       => $partner->poEffort,
            'poCosts'       => $partner->poCosts,
            'projectLeader'       => $partner->projectLeader,



           // id  identifier  projectNumber   projectName projectTitle    projectDescription  technicalArea   programme   programmeCall   primaryCluster  secondaryCluster    labelDate   cancelDate  officialStartDate   officialEndDate projectStatus   projectLeader
           //  partner partnerIdentifier   country partnerType active  coordinator selfFunded  technicalContact
           //  year    poSubmissionDate  poStatus    poTotalEffort   poTotalCosts    poEffort    poCosts poEffortInYear  poCostsInYear   poCountries
           //  fppSubmissionDate   fppStatus   fppTotalEffort  fppTotalCosts   fppEffort   fppCosts    fppEffortInYear fppCostsInYear  fppCountries
           //  latestVersionSubmissionDate latestVersionStatus latestVersionType   latestVersionTotalEffort    latestVersionTotalCosts latestVersionEffort latestVersionCosts  latestVersionEffortInYear   latestVersionCostsInYear    latestVersionCountries


        ];

        if (!$partnerData) {
            // $projectData['internal_identifier'] = ITEAOFFICE_HOST . '-' . $project->getId();


            $this->redisCache->save($cacheKey, $partnerData);
        }

        return $partnerData;

    }
}
