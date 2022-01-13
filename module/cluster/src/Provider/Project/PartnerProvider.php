<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Partner;
use Cluster\Provider\ContactProvider;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\Project\PartnerService;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use Laminas\Cache\Storage\Adapter\Redis;

use function sprintf;

class PartnerProvider implements ProviderInterface
{
    public function __construct(
        private Redis $cache,
        private ProjectProvider $projectProvider,
        private ContactProvider $contactProvider,
        private OrganisationProvider $organisationProvider,
        private PartnerService $partnerService
    ) {
    }

    #[ArrayShape([
        'id'               => "int",
        'organisation'     => "string",
        'isActive'         => "bool",
        'isSelfFunded'     => "bool",
        'isCoordinator'    => "bool",
        'technicalContact' => "array"
    ])] public static function parseCoordinatorArray(Partner $partner): array
    {
        if (!$partner->isCoordinator()) {
            throw new InvalidArgumentException(
                sprintf("%s in %s is no coordinator", $partner->getOrganisation(), $partner->getProject())
            );
        }

        return [
            'id'               => $partner->getId(),
            'organisation'     => $partner->getOrganisation()->getName(),
            'isActive'         => $partner->isActive(),
            'isSelfFunded'     => $partner->isSelfFunded(),
            'isCoordinator'    => $partner->isCoordinator(),
            'technicalContact' => $partner->getTechnicalContact(),
        ];
    }

    /**
     * @param Partner $partner
     * @return array
     * @throws \Laminas\Cache\Exception\ExceptionInterface
     */
    public function generateArray($partner): array
    {
        $cacheKey    = $partner->getResourceId();
        $partnerData = $this->cache->getItem($cacheKey);

        if (!$partnerData) {
            $partnerData = [
                'id'                  => $partner->getId(),
                'slug'                => $partner->getSlug(),
                'project'             => $this->projectProvider->generateArray($partner->getProject()),
                'isActive'            => $partner->isActive(),
                'isSelfFunded'        => $partner->isSelfFunded(),
                'isCoordinator'       => $partner->isCoordinator(),
                'technicalContact'    => $this->contactProvider->generateArray($partner->getTechnicalContact()),
                'organisation'        => $this->organisationProvider->generateArray($partner->getOrganisation()),
                'latestVersionCosts'  => $this->partnerService->parseTotalCostsByPartnerAndLatestProjectVersion(
                    $partner,
                    $partner->getProject()->getLatestVersion()
                ),
                'latestVersionEffort' => $this->partnerService->parseTotalEffortByPartnerAndLatestProjectVersion(
                    $partner,
                    $partner->getProject()->getLatestVersion()
                ),
            ];

            $this->cache->setItem($cacheKey, $partnerData);
        }

        return $partnerData;
    }

    public function generateYearArray(Partner $partner, int $year): array
    {
        $cacheKey    = $partner->getResourceId() . $year;
        $partnerData = $this->cache->getItem($cacheKey);

        if (!$partnerData) {
            $partnerData = [
                'year'                           => $year,
                'latestVersionTotalCostsInYear'  => $this->partnerService->parseTotalCostsByPartnerAndLatestProjectVersionAndYear(
                    $partner,
                    $partner->getProject()->getLatestVersion(),
                    $year
                ),
                'latestVersionTotalEffortInYear' => $this->partnerService->parseTotalEffortByPartnerAndLatestProjectVersionAndYear(
                    $partner,
                    $partner->getProject()->getLatestVersion(),
                    $year
                ),
            ];

            $this->cache->setItem($cacheKey, $partnerData);
        }

        return $partnerData;
    }
}
