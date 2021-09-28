<?php
/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Api\V1\Rest\UpdateResource;

use Cluster\Entity\Project;
use Cluster\Entity\Version\Type;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\Project\VersionService;
use Cluster\Service\ProjectService;
use Cluster\Service\StatisticsService;
use Doctrine\ORM\EntityManager;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 * Class ProjectListener
 * @package Api\V1\Rest\UpdateResource
 */
final class ProjectListener extends AbstractResourceListener
{
    private StatisticsService $statisticsService;
    private ProjectService    $projectService;
    private VersionService    $versionService;
    private PartnerService    $partnerService;
    private EntityManager     $entityManager;

    public function __construct(
        StatisticsService $statisticsService,
        ProjectService $projectService,
        VersionService $versionService,
        PartnerService $partnerService,
        EntityManager $entityManager
    ) {
        $this->statisticsService = $statisticsService;
        $this->projectService    = $projectService;
        $this->versionService    = $versionService;
        $this->partnerService    = $partnerService;
        $this->entityManager     = $entityManager;
    }

    public function create($data = []): void
    {
        //Collect all projects from the data
        $project = $this->projectService->findOrCreateProject((object) $data);

        //Delete the versions
        foreach ($project->getVersions() as $version) {
            $this->projectService->delete($version);
        }

        //Delete the partners
        foreach ($project->getPartners() as $partner) {
            $this->projectService->delete($partner);
        }

        //Collect an array of partners and specify the unique elements of these partners
        $this->extractDataFromVersion($data->versions, Type::TYPE_PO, $project);
        $this->extractDataFromVersion($data->versions, Type::TYPE_FPP, $project);
        $this->extractDataFromVersion($data->versions, Type::TYPE_LATEST, $project);
    }

    private function extractDataFromVersion(array $data, string $versionTypeName, Project $project): void
    {
        //Find the version type
        $versionType = $this->versionService->findVersionType($versionTypeName);

        //First we create the version
        $version = $this->versionService->createVersionFromData(
            (object)$data[$versionTypeName],
            $versionType,
            $project
        );

        //Now we go over the partners and collect these and save the costs and effort
        foreach ($data[$versionTypeName]['partners'] as $partnerData) {
            //Cast to an object
            $partnerData = (object)$partnerData;

            $partner = $this->partnerService->findOrCreatePartner($partnerData, $project);

            foreach ($partnerData->costs_and_effort as $year => $costsAndEffortData) {
                //This data is saved in a costs and effort table
                $costsAndEffort = new Project\Version\CostsAndEffort();
                $costsAndEffort->setVersion($version);
                $costsAndEffort->setPartner($partner);
                $costsAndEffort->setYear($year);
                $costsAndEffort->setCosts($costsAndEffortData['costs']);
                $costsAndEffort->setEffort($costsAndEffortData['effort']);

                $this->entityManager->persist($costsAndEffort);
            }
        }

        $this->entityManager->flush();
    }
}
