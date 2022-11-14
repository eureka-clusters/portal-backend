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
            $project = $this->projectService->findOrCreateProject(data: (object) $data);

            //Delete the versions
            foreach ($project->getVersions() as $version) {
                $this->projectService->delete(abstractEntity: $version);
            }

            //Delete the partners
            foreach ($project->getPartners() as $partner) {
                $this->projectService->delete(abstractEntity: $partner);
            }

            //Collect an array of partners and specify the unique elements of these partners
            $this->extractDataFromVersion(data: $data->versions, versionTypeName: Type::TYPE_PO, project: $project);
            $this->extractDataFromVersion(data: $data->versions, versionTypeName: Type::TYPE_FPP, project: $project);
            $this->extractDataFromVersion(data: $data->versions, versionTypeName: Type::TYPE_LATEST, project: $project);

            $this->entityManager->flush();
        } catch (Exception $e) {
            return new ApiProblem(status: 500, detail: $e->getMessage());
        }
    }

    private function extractDataFromVersion(array $data, string $versionTypeName, Project $project): void
    {
        if (isset($data[$versionTypeName])) {
            //Find the version type
            $versionType = $this->versionService->findVersionType(typeName: $versionTypeName);

            //First we create the version
            $version = $this->versionService->createVersionFromData(
                data: (object) $data[$versionTypeName],
                type: $versionType,
                project: $project
            );

            //Now we go over the partners and collect these and save the costs and effort
            foreach ($data[$versionTypeName]['partners'] as $partnerData) {
                //Cast to an object
                $partnerData = (object) $partnerData;

                $partner = $this->partnerService->findOrCreatePartner(data: $partnerData, project: $project);

                $partner->setIsActive(isActive: $partnerData->isActive);
                $partner->setIsCoordinator(isCoordinator: $partnerData->isCoordinator);
                $partner->setIsSelfFunded(isSelfFunded: $partnerData->isSelfFunded);
                $partner->setTechnicalContact(technicalContact: $partnerData->technicalContact);

                $totalCosts  = 0;
                $totalEffort = 0;

                foreach ($partnerData->costsAndEffort as $year => $costsAndEffortData) {
                    $totalCosts  += $costsAndEffortData['costs'];
                    $totalEffort += $costsAndEffortData['effort'];

                    //This data is saved in a costs and effort table
                    $costsAndEffort = new CostsAndEffort();
                    $costsAndEffort->setVersion(version: $version);
                    $costsAndEffort->setPartner(partner: $partner);
                    $costsAndEffort->setYear(year: $year);
                    $costsAndEffort->setCosts(costs: $costsAndEffortData['costs']);
                    $costsAndEffort->setEffort(effort: $costsAndEffortData['effort']);

                    $this->entityManager->persist(entity: $costsAndEffort);
                }

                $partner->setLatestVersionCosts(latestVersionCosts: $totalCosts);
                $partner->setLatestVersionEffort(latestVersionEffort: $totalEffort);
            }
            $this->entityManager->flush();
        }
    }
}
