<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Service\Project;

use Application\Service\AbstractService;
use Cluster\Entity;
use Cluster\Entity\Funder;
use Cluster\Entity\Project;
use Cluster\Service\CountryService;
use Cluster\Service\OrganisationService;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use stdClass;

/**
 *
 */
class PartnerService extends AbstractService
{
    private CountryService      $countryService;
    private OrganisationService $organisationService;

    public function __construct(
        EntityManager $entityManager,
        CountryService $countryService,
        OrganisationService $organisationService
    ) {
        parent::__construct($entityManager);

        $this->countryService      = $countryService;
        $this->organisationService = $organisationService;
    }

    public function findPartnerById(int $id): ?Entity\Project\Partner
    {
        return $this->entityManager->getRepository(Entity\Project\Partner::class)->find($id);
    }

    public function findPartnerBySlug(string $slug): ?Entity\Project\Partner
    {
        return $this->entityManager->getRepository(Entity\Project\Partner::class)->findOneBy(['slug' => $slug]);
    }

    public function getPartners(Funder $funder, array $filter): array
    {
        return $this->entityManager->getRepository(Entity\Project\Partner::class)->getPartnersByFunderAndFilter(
            $funder,
            $filter
        );
    }

    public function getPartnersByProject(Project $project): array
    {
        return $this->entityManager->getRepository(Entity\Project\Partner::class)->getPartnersByProject($project);
    }

    public function getPartnersByOrganisation(Entity\Organisation $organisation): array
    {
        return $this->entityManager->getRepository(Entity\Project\Partner::class)->getPartnersByOrganisation(
            $organisation
        );
    }

    public function generateFacets(Funder $funder, array $filter): array
    {
        $countries         = $this->entityManager->getRepository(Project\Partner::class)->fetchCountries(
            $funder,
            $filter
        );
        $organisationTypes = $this->entityManager->getRepository(Project\Partner::class)->fetchOrganisationTypes(
            $funder,
            $filter
        );
        $primaryClusters   = $this->entityManager->getRepository(Project\Partner::class)->fetchPrimaryClusters(
            $funder,
            $filter
        );
        $projectStatuses   = $this->entityManager->getRepository(Project\Partner::class)->fetchProjectStatuses(
            $funder,
            $filter
        );
        $years             = $this->entityManager->getRepository(Project\Partner::class)->fetchYears($funder);

        $countriesIndexed = array_map(static function (array $country) {
            return [
                'country' => $country['country'],
                'amount'  => $country[1]
            ];
        }, $countries);

        $organisationTypesIndexed = array_map(static function (array $partnerType) {
            return [
                'organisationType' => $partnerType['type'],
                'amount'           => $partnerType[1]
            ];
        }, $organisationTypes);

        $primaryClustersIndexed = array_map(static function (array $primaryCluster) {
            return [
                'primaryCluster' => $primaryCluster['name'],
                'amount'         => $primaryCluster[1]
            ];
        }, $primaryClusters);

        $projectStatusIndexed = array_map(static function (array $projectStatus) {
            return [
                'projectStatus' => $projectStatus['status'],
                'amount'        => $projectStatus[1]
            ];
        }, $projectStatuses);

        return [
            'countries'          => $countriesIndexed,
            'organisation_types' => $organisationTypesIndexed,
            'project_status'     => $projectStatusIndexed,
            'primary_clusters'   => $primaryClustersIndexed,
            'years'              => $years,
        ];
    }

    public function findOrCreatePartner(stdClass $data, Entity\Project $project): Entity\Project\Partner
    {
        //Find the country first
        $country = $this->countryService->findCountryByCd($data->country);

        if (null === $country) {
            throw new InvalidArgumentException(sprintf("Country with code %s cannot be found", $data->country));
        }

        //Find the type
        $type = $this->organisationService->findOrCreateOrganisationType($data->partner_type);

        $organisation = $this->organisationService->findOrCreateOrganisation($data->partner, $country, $type);

        //Check if we already have this partner
        $partner = $this->entityManager->getRepository(Entity\Project\Partner::class)->findOneBy(
            ['project' => $project, 'organisation' => $organisation]
        );

        if (null === $partner) {
            $partner = new Entity\Project\Partner();
            $partner->setOrganisation($organisation);

            //Save the projectName and PartnerName for slug creation
            $partner->setProjectName($project->getName());
            $partner->setOrganisationName($organisation->getName());

            $partner->setProject($project);
            $partner->setIsActive($data->active);
            $partner->setIsCoordinator($data->coordinator);
            $partner->setIsSelfFunded($data->self_funded);
            $partner->setTechnicalContact($data->technical_contact);

            $this->save($partner);
        }

        return $partner;
    }

    public function parseTotalCostsByPartnerAndLatestProjectVersion(
        Entity\Project\Partner $partner,
        Entity\Project\Version $projectVersion
    ): float {
        $repository = $this->entityManager->getRepository(Entity\Project\Version\CostsAndEffort::class);

        return $repository->parseTotalCostsByPartnerAndLatestProjectVersion($partner, $projectVersion);
    }

    public function parseTotalEffortByPartnerAndLatestProjectVersion(
        Entity\Project\Partner $partner,
        Entity\Project\Version $projectVersion
    ): float {
        $repository = $this->entityManager->getRepository(Entity\Project\Version\CostsAndEffort::class);

        return $repository->parseTotalEffortByPartnerAndLatestProjectVersion($partner, $projectVersion);
    }
}
