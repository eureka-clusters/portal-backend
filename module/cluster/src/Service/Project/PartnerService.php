<?php

declare(strict_types=1);

namespace Cluster\Service\Project;

use Admin\Entity\User;
use Application\Service\AbstractService;
use Cluster\Entity\Organisation;
use Cluster\Entity\Project;
use Cluster\Entity\Project\Partner;
use Cluster\Entity\Project\Version;
use Cluster\Entity\Project\Version\CostsAndEffort;
use Cluster\Repository\Project\PartnerRepository;
use Cluster\Service\CountryService;
use Cluster\Service\OrganisationService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Jield\Search\ValueObject\SearchFormResult;
use OpenApi\Attributes as OA;
use stdClass;
use function array_map;
use function sprintf;

class PartnerService extends AbstractService
{
    #[Pure] public function __construct(
        EntityManager                        $entityManager,
        private readonly CountryService      $countryService,
        private readonly OrganisationService $organisationService
    )
    {
        parent::__construct(entityManager: $entityManager);
    }

    public function findPartnerById(int $id): ?Partner
    {
        return $this->entityManager->getRepository(entityName: Partner::class)->find(id: $id);
    }

    public function findPartnerBySlug(string $slug): ?Partner
    {
        return $this->entityManager->getRepository(entityName: Partner::class)->findOneBy(criteria: ['slug' => $slug]);
    }

    public function getPartners(
        User             $user,
        SearchFormResult $searchFormResult,
    ): QueryBuilder
    {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Partner::class);

        return $repository->getPartnersByUserAndFilter(user: $user, searchFormResult: $searchFormResult);
    }

    public function getPartnersByProject(
        User             $user,
        Project          $project,
        SearchFormResult $searchFormResult
    ): QueryBuilder
    {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Partner::class);

        return $repository->getPartnersByProject(
            user: $user,
            project: $project,
            searchFormResult: $searchFormResult
        );
    }

    public function getPartnersByOrganisation(
        User             $user,
        Organisation     $organisation,
        SearchFormResult $searchFormResult
    ): QueryBuilder
    {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Partner::class);

        return $repository->getPartnersByOrganisation(
            user: $user,
            organisation: $organisation,
            searchFormResult: $searchFormResult
        );
    }

    public function getPartnersByProjectVersion(
        User             $user,
        Version          $projectVersion,
        SearchFormResult $searchFormResult
    ): QueryBuilder
    {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Partner::class);

        return $repository->getPartnersByProjectVersion(
            user: $user,
            projectVersion: $projectVersion,
            searchFormResult: $searchFormResult
        );
    }

    #[OA\Response(
        response: 'partner_facets',
        description: 'Project partner facets',
        content: new OA\JsonContent(ref: '#/components/schemas/partner_facets')
    )]
    #[OA\Schema(
        schema: 'partner_facets',
        title: 'Project partner facets result response',
        description: 'Array of facets for project partners',
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
                property: 'clusterGroups',
                description: 'Result of cluster groups (collection of clusters)',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/facet_content'),
            ),
            new OA\Property(
                property: 'years',
                description: 'Result of Years',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/facet_content'),
            ),
        ]
    )]
    public function generateFacets(User $user, SearchFormResult $searchFormResult): array
    {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Partner::class);

        $countries         = $repository->fetchCountries(user: $user, searchFormResult: $searchFormResult);
        $organisationTypes = $repository->fetchOrganisationTypes(user: $user, searchFormResult: $searchFormResult);
        $clusterGroups     = $repository->fetchClusterGroups();
        $projectStatuses   = $repository->fetchProjectStatuses(user: $user, searchFormResult: $searchFormResult);
        $programmeCalls    = $repository->fetchProgrammeCalls(user: $user, searchFormResult: $searchFormResult);
        $years             = $repository->fetchYears();

        $countriesIndexed = array_map(callback: static fn(array $country) => [
            'id'     => $country['id'],
            'name'   => $country['country'],
            'amount' => $country[1],
        ], array: $countries);

        $organisationTypesIndexed = array_map(callback: static fn(array $partnerType) => [
            'id'     => $partnerType['id'],
            'name'   => $partnerType['type'],
            'amount' => $partnerType[1],
        ], array: $organisationTypes);

        $clusterGroupsIndexed = array_map(callback: static fn(array $clusterGroup) => [
            'id'     => $clusterGroup['id'],
            'name'   => $clusterGroup['name'],
            'amount' => $clusterGroup[1] + $clusterGroup[2],
        ], array: $clusterGroups);

        $programmeCallIndexed = array_map(callback: static fn(array $programmeCall) => [
            'id'     => $programmeCall['programmeCall'],
            'name'   => $programmeCall['programmeCall'],
            'amount' => $programmeCall[1],
        ], array: $programmeCalls);

        $projectStatusIndexed = array_map(callback: static fn(array $projectStatus) => [
            'id'     => $projectStatus['id'],
            'name'   => $projectStatus['status'],
            'amount' => $projectStatus[1],
        ], array: $projectStatuses);

        $yearsIndexed = array_map(callback: static fn(array $years) => $years['year'], array: $years);

        return [
            'countries'         => $countriesIndexed,
            'organisationTypes' => $organisationTypesIndexed,
            'projectStatus'     => $projectStatusIndexed,
            'programmeCalls'    => $programmeCallIndexed,
            'clusterGroups'     => $clusterGroupsIndexed,
            'years'             => $yearsIndexed,
        ];
    }

    public function findOrCreatePartner(stdClass $data, Project $project): Partner
    {
        //Find the country first
        $country = $this->countryService->findCountryByCd(cd: $data->country);

        if (null === $country) {
            throw new InvalidArgumentException(
                message: sprintf("Country with code %s cannot be found", $data->country)
            );
        }

        //Find the type
        $type = $this->organisationService->findOrCreateOrganisationType(typeName: $data->type);

        $organisation = $this->organisationService->findOrCreateOrganisation(
            name: $data->partner,
            country: $country,
            type: $type
        );

        //Check if we already have this partner
        $partner = $this->entityManager->getRepository(entityName: Partner::class)->findOneBy(
            criteria: ['project' => $project, 'organisation' => $organisation]
        );

        if (null === $partner) {
            $partner = new Partner();
            $partner->setOrganisation(organisation: $organisation);

            //Save the projectName and PartnerName for slug creation
            $partner->setProjectName(projectName: $project->getName());
            $partner->setOrganisationName(organisationName: $organisation->getName());

            $partner->setProject(project: $project);
            $partner->setIsActive(isActive: $data->isActive);
            $partner->setIsCoordinator(isCoordinator: $data->isCoordinator);
            $partner->setIsSelfFunded(isSelfFunded: $data->isSelfFunded);
            $partner->setTechnicalContact(technicalContact: (array)$data->technicalContact);
            $partner->setLatestVersionEffort(latestVersionEffort: 0.0); //Create with an initial version
            $partner->setLatestVersionCosts(latestVersionCosts: 0.0); //Create with an initial version

            $this->save(entity: $partner);
        }

        return $partner;
    }

    public function findTotalCostsByPartnerAndProjectVersionAndYear(
        Partner $partner,
        Version $projectVersion,
        int     $year
    ): float
    {
        /** @var \Cluster\Repository\Project\Version\CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(entityName: CostsAndEffort::class);

        return $repository->findTotalCostsByPartnerAndProjectVersionAndYear(
            partner: $partner,
            projectVersion: $projectVersion,
            year: $year
        );
    }

    public function findTotalCostsByPartnerAndProjectVersion(
        Partner $partner,
        Version $projectVersion
    ): float
    {
        /** @var \Cluster\Repository\Project\Version\CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(entityName: CostsAndEffort::class);

        return $repository->findTotalCostsByPartnerAndProjectVersion(
            partner: $partner,
            projectVersion: $projectVersion
        );
    }

    public function findTotalEffortByPartnerAndProjectVersionAndYear(
        Partner $partner,
        Version $projectVersion,
        int     $year
    ): float
    {
        /** @var \Cluster\Repository\Project\Version\CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(entityName: CostsAndEffort::class);

        return $repository->findTotalEffortByPartnerAndProjectVersionAndYear(
            partner: $partner,
            projectVersion: $projectVersion,
            year: $year
        );
    }

    public function findTotalEffortByPartnerAndProjectVersion(
        Partner $partner,
        Version $projectVersion

    ): float
    {
        /** @var \Cluster\Repository\Project\Version\CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(entityName: CostsAndEffort::class);

        return $repository->findTotalEffortByPartnerAndProjectVersion(
            partner: $partner,
            projectVersion: $projectVersion,
        );
    }
}
