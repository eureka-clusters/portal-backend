<?php
/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Api\V1\Rest\UpdateResource;

use Cluster\Entity\Statistics\Partner;
use Cluster\Service\StatisticsService;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManager;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 * Class ProjectListener
 * @package Api\V1\Rest\UpdateResource
 */
final class ProjectListener extends AbstractResourceListener
{
    private const TYPE_PO     = 'po';
    private const TYPE_FPP    = 'fpp';
    private const TYPE_LATEST = 'latest';
    private const COSTS       = 'costs';
    private const EFFORT      = 'effort';
    private StatisticsService $statisticsService;
    private EntityManager $entityManager;
    private array $partners = [];
    private array $years = [];
    private array $totalCostsAndEffort = [];


    public function __construct(StatisticsService $statisticsService, EntityManager $entityManager)
    {
        $this->statisticsService = $statisticsService;
        $this->entityManager     = $entityManager;
    }

    public function create($data = []): void
    {
        $this->statisticsService->deletePartnersByInternalIdentifier((string)$data->internal_identifier);

        //Collect an array of partners and specify the unique elements of these partners
        $this->fetchPartnersFromVersion($data->versions[self::TYPE_PO] ?? [], self::TYPE_PO);
        $this->fetchPartnersFromVersion($data->versions[self::TYPE_FPP] ?? [], self::TYPE_FPP);
        $this->fetchPartnersFromVersion($data->versions[self::TYPE_LATEST] ?? [], self::TYPE_LATEST);

        foreach ($this->partners as $partnerId => $partnerData) {
            //We want to create a row per year, so now we go over the years, lets go
            foreach ($this->years as $year) {
                $partner = new Partner();

                //Include the general project properties
                $partner->identifier    = $data->internal_identifier;
                $partner->projectNumber = $data->project_number;
                $partner->projectName   = $data->project_name;
                $partner->projectTitle  = $data->project_title;
                if (isset($data->project_description)) {
                    $partner->projectDescription = $data->project_description;
                }
                $partner->primaryCluster = $data->primary_cluster;

                if (isset($data->secondary_cluster)) {
                    $partner->secondaryCluster = $data->secondary_cluster;
                }
                $partner->programme     = $data->programme;
                $partner->programmeCall = $data->programme_call;
                $partner->projectStatus = $data->project_status;
                $partner->projectLeader = $data->project_leader;
                if ($data->label_date) {
                    $partner->labelDate = DateTime::createFromFormat(DateTimeInterface::ATOM, $data->label_date);
                }
                if ($data->cancel_date) {
                    $partner->cancelDate = DateTime::createFromFormat(DateTimeInterface::ATOM, $data->cancel_date);
                }
                if ($data->official_start_date) {
                    $partner->officialStartDate = DateTime::createFromFormat(DateTimeInterface::ATOM, $data->official_start_date);
                }
                if ($data->official_end_date) {
                    $partner->officialEndDate = DateTime::createFromFormat(DateTimeInterface::ATOM, $data->official_end_date);
                }

                //Add the partner specific data
                $partner->partnerIdentifier = $data->internal_identifier . '-' . $partnerData['id'];
                $partner->partner           = $partnerData['partner'];
                $partner->country           = $partnerData['country'];
                $partner->partnerType       = $partnerData['partner_type'];
                $partner->active            = $partnerData['active'];
                $partner->selfFunded        = $partnerData['self_funded'];
                $partner->coordinator       = $partnerData['coordinator'];
                $partner->technicalContact  = $partnerData['technical_contact'];


                $partner->poCosts             = $this->totalCostsAndEffort[$partnerData['id']][self::TYPE_PO][self::COSTS] ?? 0;
                $partner->poEffort            = $this->totalCostsAndEffort[$partnerData['id']][self::TYPE_PO][self::EFFORT] ?? 0;
                $partner->fppCosts            = $this->totalCostsAndEffort[$partnerData['id']][self::TYPE_FPP][self::COSTS] ?? 0;
                $partner->fppEffort           = $this->totalCostsAndEffort[$partnerData['id']][self::TYPE_FPP][self::EFFORT] ?? 0;
                $partner->latestVersionCosts  = $this->totalCostsAndEffort[$partnerData['id']][self::TYPE_LATEST][self::COSTS] ?? 0;
                $partner->latestVersionEffort = $this->totalCostsAndEffort[$partnerData['id']][self::TYPE_LATEST][self::EFFORT] ?? 0;

                if (isset($data->technical_area)) {
                    $partner->technicalArea = $data->technical_area;
                }

                //Store the year
                $partner->year = $year;

                //Now we want to find the figures for each version
                if (isset($data->versions['po'])) {
                    //We have a PO now
                    $this->extractPoData($data->versions['po'], $partner, $partnerId, $year);
                }
                if (isset($data->versions['fpp'])) {
                    //We have a FPP now
                    $this->extractFppData($data->versions['fpp'], $partner, $partnerId, $year);
                }
                if (isset($data->versions['latest'])) {
                    //We have a latest version
                    $this->extractLatestVersionData($data->versions['latest'], $partner, $partnerId, $year);
                }

                $this->entityManager->persist($partner);
            }
        }

        $this->entityManager->flush();
    }

    private function fetchPartnersFromVersion(array $versionData, string $type): void
    {
        //Short-circuit the function when we have no partners
        if (!isset($versionData['partners'])) {
            return;
        }

        foreach ($versionData['partners'] as $partner) {
            $this->partners[$partner['id']] = $partner;

            //Create an element in the array for the costs and effort
            $this->totalCostsAndEffort[$partner['id']][$type][self::COSTS]  = 0;
            $this->totalCostsAndEffort[$partner['id']][$type][self::EFFORT] = 0;

            foreach ($partner['costs_and_effort'] as $year => $costsAndEffort) {
                $this->years[$year] = $year;

                //Sum the costs and effort
                $this->totalCostsAndEffort[$partner['id']][$type][self::COSTS]  += $costsAndEffort['costs'];
                $this->totalCostsAndEffort[$partner['id']][$type][self::EFFORT] += $costsAndEffort['effort'];
            }
        }
    }

    private function extractPoData(array $poData, Partner $partner, int $partnerId, int $year): void
    {
        if (isset($poData['submission_date'])) {
            $partner->poSubmissionDate = DateTime::createFromFormat(DateTimeInterface::ATOM, $poData['submission_date']);
        }
        $partner->poStatus      = $poData['status'];
        $partner->poTotalEffort = $poData['total_effort'];
        $partner->poTotalCosts  = $poData['total_costs'];
        $partner->poCountries   = $poData['countries'];

        //Now find the costs and effort per year
        foreach ($poData['partners'] as $partnerData) {
            if ((int)$partnerData['id'] === $partnerId) {
                foreach ($partnerData['costs_and_effort'] as $costsAndEffortYear => $costsAndEffort) {
                    //Only keep the data when the year is the same
                    if ($costsAndEffortYear === $year) {
                        $partner->poCostsInYear  = $costsAndEffort['costs'];
                        $partner->poEffortInYear = $costsAndEffort['effort'];
                    }
                }
            }
        }
    }

    private function extractFppData(array $fppData, Partner $partner, int $partnerId, int $year): void
    {
        if (isset($fppData['submission_date'])) {
            $partner->fppSubmissionDate = DateTime::createFromFormat(DateTimeInterface::ATOM, $fppData['submission_date']);
        }
        $partner->fppStatus      = $fppData['status'];
        $partner->fppTotalEffort = $fppData['total_effort'];
        $partner->fppTotalCosts  = $fppData['total_costs'];
        $partner->fppCountries   = $fppData['countries'];

        //Now find the costs and effort per year
        foreach ($fppData['partners'] as $partnerData) {
            if ((int)$partnerData['id'] === $partnerId) {
                foreach ($partnerData['costs_and_effort'] as $costsAndEffortYear => $costsAndEffort) {
                    //Only keep the data when the year is the same
                    if ($costsAndEffortYear === $year) {
                        $partner->fppCostsInYear  = $costsAndEffort['costs'];
                        $partner->fppEffortInYear = $costsAndEffort['effort'];
                    }
                }
            }
        }
    }

    private function extractLatestVersionData(array $latestVersionData, Partner $partner, int $partnerId, int $year): void
    {
        if (isset($latestVersionData['submission_date'])) {
            $partner->latestVersionSubmissionDate = DateTime::createFromFormat(DateTimeInterface::ATOM, $latestVersionData['submission_date']);
        }
        $partner->latestVersionType        = $latestVersionData['type'];
        $partner->latestVersionStatus      = $latestVersionData['status'];
        $partner->latestVersionTotalEffort = $latestVersionData['total_effort'];
        $partner->latestVersionTotalCosts  = $latestVersionData['total_costs'];
        $partner->latestVersionCountries   = $latestVersionData['countries'];

        //Now find the costs and effort per year
        foreach ($latestVersionData['partners'] as $partnerData) {
            if ((int)$partnerData['id'] === $partnerId) {
                foreach ($partnerData['costs_and_effort'] as $costsAndEffortYear => $costsAndEffort) {
                    //Only keep the data when the year is the same
                    if ($costsAndEffortYear === $year) {
                        $partner->latestVersionCostsInYear  = $costsAndEffort['costs'];
                        $partner->latestVersionEffortInYear = $costsAndEffort['effort'];
                    }
                }
            }
        }
    }
}
