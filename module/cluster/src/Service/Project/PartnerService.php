<?php

declare(strict_types=1);

namespace Cluster\Service\Project;

use Admin\Entity\User;
use Application\Service\AbstractService;
use Cluster\Entity\Funder;
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
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use stdClass;

use function array_map;
use function sprintf;

class PartnerService extends AbstractService
{
    #[Pure] public function __construct(
        EntityManager $entityManager,
        private readonly CountryService $countryService,
        private readonly OrganisationService $organisationService
    ) {
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
        User $user,
        array $filter,
        string $sort = 'partner.organisation.name',
        string $order = 'asc'
    ): QueryBuilder {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Partner::class);

        return $repository->getPartnersByUserAndFilter(user: $user, filter: $filter, sort:  $sort, order: $order);
    }

    public function getPartnersByProject(Project $project): QueryBuilder
    {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Partner::class);

        return $repository->getPartnersByProject(project: $project);
    }

    public function getPartnersByOrganisation(Organisation $organisation): QueryBuilder
    {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Partner::class);

        return $repository->getPartnersByOrganisation(organisation: $organisation);
    }

    #[ArrayShape(shape: [
        'countries' => "array[]",
        'organisationTypes' => "array[]",
        'projectStatus' => "array[]",
        'clusters' => "array[]",
        'programmeCalls' => "array[]",
        'years' => "array"
    ])] public function generateFacets(User $user, array $filter): array
    {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: Partner::class);

        $countries = $repository->fetchCountries(user: $user, filter: $filter);
        $organisationTypes = $repository->fetchOrganisationTypes(user: $user, filter: $filter);
        $clusters = $repository->fetchClusters();
        $projectStatuses = $repository->fetchProjectStatuses(user: $user, filter: $filter);
        $programmeCalls = $repository->fetchProgrammeCalls(user: $user, filter: $filter);
        $years = $repository->fetchYears();

        $countriesIndexed = array_map(callback: static fn(array $country) => [
            'name' => $country['country'],
            'amount' => $country[1],
        ], array: $countries);

        $organisationTypesIndexed = array_map(callback: static fn(array $partnerType) => [
            'name' => $partnerType['type'],
            'amount' => $partnerType[1],
        ], array: $organisationTypes);

        $clustersIndexed = array_map(callback: static fn(array $cluster) => [
            'name' => $cluster['name'],
            'amount' => $cluster[1] + $cluster[2],
        ], array: $clusters);

        $programmeCallIndexed = array_map(callback: static fn(array $programmeCall) => [
            'name' => $programmeCall['programmeCall'],
            'amount' => $programmeCall[1],
        ], array: $programmeCalls);

        $projectStatusIndexed = array_map(callback: static fn(array $projectStatus) => [
            'name' => $projectStatus['status'],
            'amount' => $projectStatus[1],
        ], array: $projectStatuses);

        $yearsIndexed = array_map(callback: static fn(array $years) => $years['year'], array: $years);

        return [
            'countries' => $countriesIndexed,
            'organisationTypes' => $organisationTypesIndexed,
            'projectStatus' => $projectStatusIndexed,
            'clusters' => $clustersIndexed,
            'programmeCalls' => $programmeCallIndexed,
            'years' => $yearsIndexed,
        ];
    }

    public function findOrCreatePartner(stdClass $data, Project $project): Partner
    {
        //Find the country first
        $country = $this->countryService->findCountryByCd(cd: $data->country);

        if (null === $country) {
            throw new InvalidArgumentException(message: sprintf("Country with code %s cannot be found", $data->country));
        }

        //Find the type
        $type = $this->organisationService->findOrCreateOrganisationType(typeName: $data->type);

        $organisation = $this->organisationService->findOrCreateOrganisation(
            name: $data->partner,
            country: $country,
            type: $type);

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
            $partner->setTechnicalContact(technicalContact: $data->technicalContact);
            $partner->setLatestVersionEffort(latestVersionEffort: 0.0); //Create with an initial version
            $partner->setLatestVersionCosts(latestVersionCosts: 0.0); //Create with an initial version

            $this->save(entity: $partner);
        }

        return $partner;
    }

    public function findTotalCostsByPartnerAndLatestProjectVersionAndYear(
        Partner $partner,
        Version $projectVersion,
        int $year
    ): float {
        /** @var \Cluster\Repository\Project\Version\CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(entityName: CostsAndEffort::class);

        return $repository->findTotalCostsByPartnerAndLatestProjectVersionAndYear(
            partner: $partner,
            projectVersion: $projectVersion,
            year: $year);
    }

    public function findTotalEffortByPartnerAndLatestProjectVersionAndYear(
        Partner $partner,
        Version $projectVersion,
        int $year
    ): float {
        /** @var \Cluster\Repository\Project\Version\CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(entityName: CostsAndEffort::class);

        return $repository->findTotalEffortByPartnerAndLatestProjectVersionAndYear(
            partner: $partner,
            projectVersion: $projectVersion,
            year: $year);
    }

}
