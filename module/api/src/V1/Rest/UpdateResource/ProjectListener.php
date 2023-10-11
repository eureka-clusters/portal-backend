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
use Laminas\Json\Json;
use OpenApi\Attributes as OA;
use stdClass;

final class ProjectListener extends AbstractResourceListener
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly VersionService $versionService,
        private readonly PartnerService $partnerService,
        private readonly EntityManager  $entityManager
    )
    {
    }

    #[OA\Post(
        path: '/api/update/project',
        description: 'Update project information',
        summary: 'Update project information from backends',
        requestBody: new OA\RequestBody(
            description: "Content",
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        required: ['file'],
                        properties: [
                            new OA\Property(property: 'file', description: 'Json file with project information', type: 'string', format: 'binary')
                        ],
                    )
                ),
            ]
        ),
        tags: ['Project'],
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 201, description: 'Created'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ],
    )]
    public function create($data = []): ApiProblem|string
    {
        $filter  = $this->getInputFilter();
        $content = $filter->getValue('file');

        $data = file_get_contents($content['tmp_name']);
        $data = Json::decode($data, Json::TYPE_OBJECT);

        try {
            //Collect all projects from the data
            $project = $this->projectService->findOrCreateProject(data: $data);

            //Delete the versions
            foreach ($project->getVersions() as $version) {
                $this->projectService->delete(entity: $version);
            }

            //Delete the partners
            foreach ($project->getPartners() as $partner) {
                $this->projectService->delete(entity: $partner);
            }

            //Collect an array of partners and specify the unique elements of these partners
            $this->extractDataFromVersion(data: $data->versions, versionTypeName: Type::TYPE_PO, project: $project);
            $this->extractDataFromVersion(data: $data->versions, versionTypeName: Type::TYPE_FPP, project: $project);
            $this->extractDataFromVersion(data: $data->versions, versionTypeName: Type::TYPE_LATEST, project: $project);

            //Update the costs/effort totals for all the project
            $this->projectService->updateProjectCostsAndEffort(project: $project);

            $this->entityManager->flush();
        } catch (Exception $e) {
            return new ApiProblem(status: 500, detail: $e->getMessage());
        }

        return '';
    }

    private function extractDataFromVersion(stdClass $data, string $versionTypeName, Project $project): void
    {
        //Convert to an array

        if (isset($data->$versionTypeName)) {
            //Find the version type
            $versionType = $this->versionService->findVersionType(typeName: $versionTypeName);

            //First we create the version
            $version = $this->versionService->createVersionFromData(
                data: $data->$versionTypeName,
                type: $versionType,
                project: $project
            );

            $totalVersionCosts  = 0;
            $totalVersionEffort = 0;

            //Now we go over the partners and collect these and save the costs and effort
            foreach ($data->$versionTypeName->partners as $partnerData) {
                $partner = $this->partnerService->findOrCreatePartner(data: $partnerData, project: $project);

                $partner->setIsActive(isActive: $partnerData->isActive);
                $partner->setIsCoordinator(isCoordinator: $partnerData->isCoordinator);
                $partner->setIsSelfFunded(isSelfFunded: $partnerData->isSelfFunded);
                $partner->setTechnicalContact(technicalContact: (array)$partnerData->technicalContact);

                $totalCosts  = 0;
                $totalEffort = 0;

                foreach ($partnerData->costsAndEffort as $year => $costsAndEffortData) {

                    $totalCosts  += $costsAndEffortData->costs;
                    $totalEffort += $costsAndEffortData->effort;

                    //This data is saved in a costs and effort table
                    $costsAndEffort = new CostsAndEffort();
                    $costsAndEffort->setVersion(version: $version);
                    $costsAndEffort->setPartner(partner: $partner);
                    $costsAndEffort->setYear(year: (int)$year);
                    $costsAndEffort->setCosts(costs: $costsAndEffortData->costs);
                    $costsAndEffort->setEffort(effort: $costsAndEffortData->effort);

                    $this->entityManager->persist(entity: $costsAndEffort);
                }

                if ($versionType->isPo()) {
                    $partner->setProjectOutlineCosts(projectOutlineCosts: $totalCosts);
                    $partner->setProjectOutlineEffort(projectOutlineEffort: $totalEffort);
                }

                if ($versionType->isFpp()) {
                    $partner->setFullProjectProposalCosts(fullProjectProposalCosts: $totalCosts);
                    $partner->setFullProjectProposalEffort(fullProjectProposalEffort: $totalEffort);
                }

                if ($versionType->isLatest()) {
                    $partner->setLatestVersionCosts(latestVersionCosts: $totalCosts);
                    $partner->setLatestVersionEffort(latestVersionEffort: $totalEffort);
                }

                $totalVersionCosts  += $totalCosts;
                $totalVersionEffort += $totalEffort;
            }

            $version->setCosts($totalVersionCosts);
            $version->setEffort($totalVersionEffort);

            $this->entityManager->persist(entity: $version);

            $this->entityManager->flush();
        }
    }
}
