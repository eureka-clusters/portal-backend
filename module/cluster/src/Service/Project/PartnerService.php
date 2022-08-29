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
        parent::__construct($entityManager);
    }

    public function findPartnerById(int $id): ?Partner
    {
        return $this->entityManager->getRepository(Partner::class)->find($id);
    }

    public function findPartnerBySlug(string $slug): ?Partner
    {
        return $this->entityManager->getRepository(Partner::class)->findOneBy(['slug' => $slug]);
    }

    public function getPartners(
        User $user,
        array $filter,
        string $sort = 'partner.organisation.name',
        string $order = 'asc'
    ): QueryBuilder {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(Partner::class);

        return $repository->getPartnersByUserAndFilter(user: $user, filter: $filter, sort:  $sort, order: $order);
    }

    public function getPartnersByProject(Project $project): QueryBuilder
    {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(Partner::class);

        return $repository->getPartnersByProject($project);
    }

    public function getPartnersByOrganisation(Organisation $organisation): QueryBuilder
    {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(Partner::class);

        return $repository->getPartnersByOrganisation($organisation);
    }

    #[ArrayShape([
        'countries' => "array[]",
        'organisationTypes' => "array[]",
        'projectStatus' => "array[]",
        'clusters' => "array[]",
        'programmeCalls' => "array[]",
        'years' => "array"
    ])] public function generateFacets(User $user, array $filter): array
    {
        /** @var PartnerRepository $repository */
        $repository = $this->entityManager->getRepository(Partner::class);

        $countries = $repository->fetchCountries(user: $user, filter: $filter);
        $organisationTypes = $repository->fetchOrganisationTypes(user: $user, filter: $filter);
        $clusters = $repository->fetchClusters();
        $projectStatuses = $repository->fetchProjectStatuses(user: $user, filter: $filter);
        $programmeCalls = $repository->fetchProgrammeCalls(user: $user, filter: $filter);
        $years = $repository->fetchYears();

        $countriesIndexed = array_map(static fn(array $country) => [
            'name' => $country['country'],
            'amount' => $country[1],
        ], $countries);

        $organisationTypesIndexed = array_map(static fn(array $partnerType) => [
            'name' => $partnerType['type'],
            'amount' => $partnerType[1],
        ], $organisationTypes);

        $clustersIndexed = array_map(static fn(array $cluster) => [
            'name' => $cluster['name'],
            'amount' => $cluster[1] + $cluster[2],
        ], $clusters);

        $programmeCallIndexed = array_map(static fn(array $programmeCall) => [
            'name' => $programmeCall['programmeCall'],
            'amount' => $programmeCall[1],
        ], $programmeCalls);

        $projectStatusIndexed = array_map(static fn(array $projectStatus) => [
            'name' => $projectStatus['status'],
            'amount' => $projectStatus[1],
        ], $projectStatuses);

        $yearsIndexed = array_map(static fn(array $years) => $years['year'], $years);

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
        $country = $this->countryService->findCountryByCd($data->country);

        if (null === $country) {
            throw new InvalidArgumentException(sprintf("Country with code %s cannot be found", $data->country));
        }

        //Find the type
        $type = $this->organisationService->findOrCreateOrganisationType($data->type);

        $organisation = $this->organisationService->findOrCreateOrganisation($data->partner, $country, $type);

        //Check if we already have this partner
        $partner = $this->entityManager->getRepository(Partner::class)->findOneBy(
            ['project' => $project, 'organisation' => $organisation]
        );

        if (null === $partner) {
            $partner = new Partner();
            $partner->setOrganisation($organisation);

            //Save the projectName and PartnerName for slug creation
            $partner->setProjectName($project->getName());
            $partner->setOrganisationName($organisation->getName());

            $partner->setProject($project);
            $partner->setIsActive($data->isActive);
            $partner->setIsCoordinator($data->isCoordinator);
            $partner->setIsSelfFunded($data->isSelfFunded);
            $partner->setTechnicalContact($data->technicalContact);
            $partner->setLatestVersionEffort(0.0); //Create with an initial version
            $partner->setLatestVersionCosts(0.0); //Create with an initial version

            $this->save($partner);
        }

        return $partner;
    }

    public function findTotalCostsByPartnerAndLatestProjectVersionAndYear(
        Partner $partner,
        Version $projectVersion,
        int $year
    ): float {
        /** @var \Cluster\Repository\Project\Version\CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(CostsAndEffort::class);

        return $repository->findTotalCostsByPartnerAndLatestProjectVersionAndYear($partner, $projectVersion, $year);
    }

    public function findTotalEffortByPartnerAndLatestProjectVersionAndYear(
        Partner $partner,
        Version $projectVersion,
        int $year
    ): float {
        /** @var \Cluster\Repository\Project\Version\CostsAndEffort $repository */
        $repository = $this->entityManager->getRepository(CostsAndEffort::class);

        return $repository->findTotalEffortByPartnerAndLatestProjectVersionAndYear($partner, $projectVersion, $year);
    }

}
