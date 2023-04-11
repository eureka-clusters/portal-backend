<?php

declare(strict_types=1);

namespace Cluster\Service;

use Admin\Entity\User;
use Application\Service\AbstractService;
use Cluster\Entity\Project;
use Cluster\Entity\Project\Status;
use Cluster\Repository\ProjectRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\Pure;
use Jield\Search\ValueObject\SearchFormResult;
use OpenApi\Attributes as OA;
use stdClass;

use function array_map;
use function ceil;

class ProjectService extends AbstractService
{
    final public const DURATION_MONTH = 'm';
    final public const DURATION_YEAR = 'y';
    final public const DURATION_DAYS = 'd';

    #[Pure] public function __construct(
        EntityManager $entityManager,
        private readonly ClusterService $clusterService
    ) {
        parent::__construct(entityManager: $entityManager);
    }

    public function getProjects(
        User $user,
        SearchFormResult $searchFormResult
    ): QueryBuilder {
        /** @var ProjectRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Project::class);

        return $repository->getProjectsByUserAndFilter(user: $user, searchFormResult: $searchFormResult);
    }

    public function searchProjects(User $user, ?string $query, int $limit): array
    {
        /** @var ProjectRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Project::class);

        return $repository->searchProjects(user: $user, query: $query, limit: $limit)->getQuery()->getResult();
    }

    #[OA\Response(
        response: 'project_facets',
        description: 'Project facets',
        content: new OA\JsonContent(ref: '#/components/schemas/project_facets')
    )]
    #[OA\Schema(
        schema: 'project_facets',
        title: 'Project facets result response',
        description: 'Array of facets for projects',
        properties: [
            new OA\Property(
                property: 'countries',
                description: 'Result of countries',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/facet_content'),
            ),
            new OA\Property(
                property: 'organisationTypes',
                description: 'Result of organisation types',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/facet_content'),
            ),
            new OA\Property(
                property: 'projectStatus',
                description: 'Result of project status',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/facet_content'),
            ),
            new OA\Property(
                property: 'programmeCalls',
                description: 'Result of programme calls',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/facet_content'),
            ),
            new OA\Property(
                property: 'clusters',
                description: 'Result of clusters',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/facet_content'),
            ),
        ]
    )]
    #[OA\Schema(
        schema: 'facet_content',
        title: 'Facets content',
        description: 'Response per facet',
        properties: [
            new OA\Property(
                property: 'name',
                description: 'Name of the facet',
                type: 'string',
                example: 'Facet label'
            ),
            new OA\Property(
                property: 'amount',
                description: 'Amount of results in the facet',
                type: 'integer',
                example: 4
            ),
        ])]
    public function generateFacets(User $user, SearchFormResult $searchFormResult): array
    {
        /** @var ProjectRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Project::class);

        $countries         = $repository->fetchCountries(user: $user, searchFormResult: $searchFormResult);
        $organisationTypes = $repository->fetchOrganisationTypes(user: $user, searchFormResult: $searchFormResult);
        $programmeCalls    = $repository->fetchProgrammeCalls(user: $user, searchFormResult: $searchFormResult);
        $clusters          = $repository->fetchClusters(searchFormResult: $searchFormResult);
        $projectStatuses   = $repository->fetchProjectStatuses(user: $user, searchFormResult: $searchFormResult);

        $countriesIndexed = array_map(callback: static fn (array $country) => [
            'name'   => $country['country'],
            'amount' => $country[1],
        ], array: $countries);

        $organisationTypesIndexed = array_map(callback: static fn (array $organisationType) => [
            'name'   => $organisationType['type'],
            'amount' => $organisationType[1],
        ], array: $organisationTypes);

        $clustersIndexed = array_map(callback: static fn (array $cluster) => [
            'name'   => $cluster['name'],
            'amount' => $cluster[1] + $cluster[2],
        ], array: $clusters);

        $programmeCallsIndexed = array_map(callback: static fn (array $programmeCall) => [
            'name'   => $programmeCall['programmeCall'],
            'amount' => $programmeCall[1],
        ], array: $programmeCalls);

        $projectStatusIndexed = array_map(callback: static fn (array $projectStatus) => [
            'name'   => $projectStatus['status'],
            'amount' => $projectStatus[1],
        ], array: $projectStatuses);

        return [
            'countries'         => $countriesIndexed,
            'organisationTypes' => $organisationTypesIndexed,
            'projectStatus'     => $projectStatusIndexed,
            'programmeCalls'    => $programmeCallsIndexed,
            'clusters'          => $clustersIndexed,
        ];
    }

    public function findOrCreateProject(stdClass $data): Project
    {
        $project = $this->findProjectByIdentifier(identifier: $data->internalIdentifier);

        //If we cannot find the project we create a new one. Only set the identifier as we will later overwrite/update the properties
        if (null === $project) {
            $project = new Project();
            $project->setIdentifier(identifier: $data->internalIdentifier);
        }

        $project->setNumber(number: $data->number);
        $project->setName(name: $data->name);
        $project->setTitle(title: $data->title);
        $project->setDescription(description: $data->description);
        $project->setProgramme(programme: $data->programme);
        $project->setProgrammeCall(programmeCall: $data->programmeCall);

        $project->setProjectLeader(projectLeader: $data->projectLeader);
        $project->setTechnicalArea(technicalArea: $data->technicalArea);

        //Find or create the primary cluster
        $primaryCluster = $this->clusterService->findOrCreateCluster(clusterData: $data->primaryCluster);

        $project->setPrimaryCluster(primaryCluster: $primaryCluster);

        if (isset($data->secondaryCluster)) {
            $secondaryCluster = $this->clusterService->findOrCreateCluster(clusterData: $data->secondaryCluster);
            $project->setSecondaryCluster(secondaryCluster: $secondaryCluster);
        }

        //Find the status
        $status = $this->entityManager->getRepository(entityName: Status::class)->findOneBy(
            criteria: ['status' => $data->projectStatus]
        );

        //If we cannot find the status, we create a new one
        if (null === $status) {
            $status = new Status();
            $status->setStatus(status: $data->projectStatus);
        }

        $project->setStatus(status: $status);

        //Handle the dates
        if ($data->officialStartDate) {
            $officialStartDate = DateTime::createFromFormat(
                format: DateTimeInterface::ATOM,
                datetime: $data->officialStartDate
            );
            $project->setOfficialStartDate(officialStartDate: $officialStartDate ?: null);
        }

        if ($data->officialEndDate) {
            $officialEndDate = DateTime::createFromFormat(
                format: DateTimeInterface::ATOM,
                datetime: $data->officialEndDate
            );
            $project->setOfficialEndDate(officialEndDate: $officialEndDate ?: null);
        }

        if ($data->labelDate) {
            $labelDate = DateTime::createFromFormat(format: DateTimeInterface::ATOM, datetime: $data->labelDate);
            $project->setLabelDate(labelDate: $labelDate ?: null);
        }

        if ($data->cancelDate) {
            $cancelDate = DateTime::createFromFormat(
                format: DateTimeInterface::ATOM,
                datetime: (string)$data->cancelDate
            );
            $project->setCancelDate(cancelDate: $cancelDate ?: null);
        }

        $this->save(entity: $project);
        return $project;
    }

    public function findProjectByIdentifier(string $identifier): ?Project
    {
        return $this->entityManager->getRepository(entityName: Project::class)->findOneBy(
            criteria: ['identifier' => $identifier]
        );
    }

    public function findProjectBySlug(string $slug): ?Project
    {
        return $this->entityManager->getRepository(entityName: Project::class)->findOneBy(
            criteria: ['slug' => $slug]
        );
    }

    public function findProjectBySlugAndUser(string $slug, User $user): ?Project
    {
        /** @var ProjectRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Project::class);
        return $repository->findProjectBySlugAndUser(slug: $slug, user: $user)->getQuery()->getOneOrNullResult();
    }

    public function parseDuration(Project $project, string $type = self::DURATION_MONTH): ?int
    {
        if (null === $project->getOfficialStartDate() || null === $project->getOfficialEndDate()) {
            return null;
        }

        $difference = $project->getOfficialEndDate()->diff(targetObject: $project->getOfficialStartDate());

        return match ($type) {
            self::DURATION_YEAR => (int)(
                (int)$difference->format(format: '%' . self::DURATION_YEAR) + ceil(
                    num: (int)($difference->format(format: '%' . self::DURATION_MONTH)) / 12
                )
            ),
            self::DURATION_MONTH => (
                    (int)($difference->format(format: '%' . self::DURATION_YEAR)) * 12
                ) +
                (int)$difference->format(format: '%' . self::DURATION_MONTH) +
                ($difference->format(format: '%' . self::DURATION_DAYS) > 0 ? 1
                    : 0),
            default => ((int)($difference->format(
                        format: '%' . self::DURATION_YEAR
                    )) * 365) + ((int)($difference->format(
                        format: '%' . self::DURATION_MONTH
                    )) * 12) + (int)$difference->format(format: '%' . self::DURATION_DAYS),
        };
    }
}
