<?php

declare(strict_types=1);

namespace Cluster\Service;

use Application\Service\AbstractService;
use Cluster\Entity\Funder;
use Cluster\Entity\Project;
use Cluster\Entity\Project\Status;
use Cluster\Repository\ProjectRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use stdClass;

use function array_map;

class ProjectService extends AbstractService
{
    #[Pure] public function __construct(EntityManager $entityManager, private ClusterService $clusterService)
    {
        parent::__construct($entityManager);
    }

    public function getProjects(
        Funder $funder,
        array $filter,
        string $sort = 'project.name',
        string $order = 'asc'
    ): QueryBuilder {
        /** @var ProjectRepository $repository */
        $repository = $this->entityManager->getRepository(Project::class);

        return $repository->getProjectsByFunderAndFilter($funder, $filter, $sort, $order);
    }

    #[ArrayShape([
        'countries'         => "array[]",
        'organisationTypes' => "array[]",
        'projectStatus'     => "array[]",
        'programmeCalls'    => "array[]",
        'primaryClusters'   => "array[]"
    ])] public function generateFacets(Funder $funder, array $filter): array
    {
        /** @var ProjectRepository $repository */
        $repository = $this->entityManager->getRepository(Project::class);

        $countries         = $repository->fetchCountries($funder, $filter);
        $organisationTypes = $repository->fetchOrganisationTypes($funder, $filter);
        $primaryClusters   = $repository->fetchPrimaryClusters($funder, $filter);
        $programmeCalls    = $repository->fetchProgrammeCalls($funder, $filter);
        $projectStatuses   = $repository->fetchProjectStatuses($funder, $filter);

        $countriesIndexed = array_map(static fn(array $country) => [
            'name'   => $country['country'],
            'amount' => $country[1],
        ], $countries);

        $organisationTypesIndexed = array_map(static fn(array $organisationType) => [
            'name'   => $organisationType['type'],
            'amount' => $organisationType[1],
        ], $organisationTypes);

        $primaryClustersIndexed = array_map(static fn(array $primaryCluster) => [
            'name'   => $primaryCluster['name'],
            'amount' => $primaryCluster[1],
        ], $primaryClusters);

        $programmeCallsIndexed = array_map(static fn(array $programmeCall) => [
            'name'   => $programmeCall['programmeCall'],
            'amount' => $programmeCall[1],
        ], $programmeCalls);

        $projectStatusIndexed = array_map(static fn(array $projectStatus) => [
            'name'   => $projectStatus['status'],
            'amount' => $projectStatus[1],
        ], $projectStatuses);

        return [
            'countries'         => $countriesIndexed,
            'organisationTypes' => $organisationTypesIndexed,
            'projectStatus'     => $projectStatusIndexed,
            'programmeCalls'    => $programmeCallsIndexed,
            'primaryClusters'   => $primaryClustersIndexed,
        ];
    }

    public function findOrCreateProject(stdClass $data): Project
    {
        $project = $this->findProjectByIdentifier($data->internalIdentifier);

        //If we cannot find the project we create a new one. Only set the identifier as we will later overwrite/update the properties
        if (null === $project) {
            $project = new Project();
            $project->setIdentifier($data->internalIdentifier);
        }

        $project->setNumber($data->number);
        $project->setName($data->name);
        $project->setTitle($data->title);
        $project->setDescription($data->description);
        $project->setProgramme($data->programme);
        $project->setProgrammeCall($data->programmeCall);

        $project->setProjectLeader($data->projectLeader);
        $project->setTechnicalArea($data->technicalArea);

        //Find or create the primary cluster
        $primaryCluster = $this->clusterService->findOrCreateCluster($data->primaryCluster);

        $project->setPrimaryCluster($primaryCluster);

        if (isset($data->secondaryCluster)) {
            $secondaryCluster = $this->clusterService->findOrCreateCluster($data->secondaryCluster);
            $project->setSecondaryCluster($secondaryCluster);
        }

        //Find the status
        $status = $this->entityManager->getRepository(Status::class)->findOneBy(
            ['status' => $data->projectStatus]
        );

        //If we cannot find the status, we create a new one
        if (null === $status) {
            $status = new Status();
            $status->setStatus($data->projectStatus);
        }

        $project->setStatus($status);

        //Handle the dates
        if ($data->officialStartDate) {
            $officialStartDate = DateTime::createFromFormat(DateTimeInterface::ATOM, $data->officialStartDate);
            $project->setOfficialStartDate($officialStartDate ?: null);
        }

        if ($data->officialEndDate) {
            $officialEndDate = DateTime::createFromFormat(DateTimeInterface::ATOM, $data->officialEndDate);
            $project->setOfficialEndDate($officialEndDate ?: null);
        }

        if ($data->labelDate) {
            $labelDate = DateTime::createFromFormat(DateTimeInterface::ATOM, $data->labelDate);
            $project->setLabelDate($labelDate ?: null);
        }

        if ($data->cancelDate) {
            $cancelDate = DateTime::createFromFormat(DateTimeInterface::ATOM, (string)$data->cancelDate);
            $project->setCancelDate($cancelDate ?: null);
        }

        $this->save($project);
        return $project;
    }

    public function findProjectByIdentifier(string $identifier): ?Project
    {
        return $this->entityManager->getRepository(Project::class)->findOneBy(
            ['identifier' => $identifier]
        );
    }

    public function findProjectBySlug(string $slug): ?Project
    {
        return $this->entityManager->getRepository(Project::class)->findOneBy(
            ['slug' => $slug]
        );
    }
}
