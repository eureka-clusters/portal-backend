<?php

declare(strict_types=1);

namespace Api\V1\Rest\UpdateResource;

use Cluster\Entity\Project;
use Cluster\Entity\Project\Version\CostsAndEffort;
use Cluster\Entity\Version\Type;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\Project\VersionService;
use Cluster\Service\ProjectService;
use Doctrine\ORM\EntityManager;
use Exception;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;

final class ProjectListener extends AbstractResourceListener
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly VersionService $versionService,
        private readonly PartnerService $partnerService,
        private readonly EntityManager $entityManager
    ) {
    }

    public function create($data = [])
    {
        try {
            //Collect all projects from the data
            $project = $this->projectService->findOrCreateProject((object)$data);

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

            $this->entityManager->flush();
        } catch (Exception $e) {
            return new ApiProblem(500, $e->getMessage());
        }
    }

    private function extractDataFromVersion(array $data, string $versionTypeName, Project $project): void
    {
        if (isset($data[$versionTypeName])) {
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

                $partner->setIsActive($partnerData->isActive);
                $partner->setIsCoordinator($partnerData->isCoordinator);
                $partner->setIsSelfFunded($partnerData->isSelfFunded);
                $partner->setTechnicalContact($partnerData->technicalContact);

                $totalCosts = 0;
                $totalEffort = 0;

                foreach ($partnerData->costsAndEffort as $year => $costsAndEffortData) {
                    $totalCosts += $costsAndEffortData['costs'];
                    $totalEffort += $costsAndEffortData['effort'];

                    //This data is saved in a costs and effort table
                    $costsAndEffort = new CostsAndEffort();
                    $costsAndEffort->setVersion($version);
                    $costsAndEffort->setPartner($partner);
                    $costsAndEffort->setYear($year);
                    $costsAndEffort->setCosts($costsAndEffortData['costs']);
                    $costsAndEffort->setEffort($costsAndEffortData['effort']);

                    $this->entityManager->persist($costsAndEffort);
                }

                $partner->setLatestVersionCosts($totalCosts);
                $partner->setLatestVersionEffort($totalEffort);
            }
            $this->entityManager->flush();
        }
    }
}
