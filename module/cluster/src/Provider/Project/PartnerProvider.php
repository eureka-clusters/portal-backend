<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Partner;
use Cluster\Provider\ContactProvider;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\ProjectProvider;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use Laminas\Cache\Storage\Adapter\Redis;

use function sprintf;

class PartnerProvider implements ProviderInterface
{
    public function __construct(
        private readonly Redis $cache,
        private readonly ProjectProvider $projectProvider,
        private readonly ContactProvider $contactProvider,
        private readonly OrganisationProvider $organisationProvider
    ) {
    }

    #[ArrayShape([
        'id' => "int",
        'organisation' => "string",
        'isActive' => "bool",
        'isSelfFunded' => "bool",
        'isCoordinator' => "bool",
        'technicalContact' => "array"
    ])] public static function parseCoordinatorArray(Partner $partner): array
    {
        if (!$partner->isCoordinator()) {
            throw new InvalidArgumentException(
                sprintf("%s in %s is no coordinator", $partner->getOrganisation(), $partner->getProject())
            );
        }

        return [
            'id' => $partner->getId(),
            'organisation' => $partner->getOrganisation()->getName(),
            'isActive' => $partner->isActive(),
            'isSelfFunded' => $partner->isSelfFunded(),
            'isCoordinator' => $partner->isCoordinator(),
            'technicalContact' => $partner->getTechnicalContact(),
        ];
    }

    public function generateArray($partner): array
    {
        $cacheKey = $partner->getResourceId();
        $partnerData = $this->cache->getItem($cacheKey);

        if (!$partnerData) {
            $partnerData = [
                'id' => $partner->getId(),
                'slug' => $partner->getSlug(),
                'project' => $this->projectProvider->generateArray($partner->getProject()),
                'isActive' => $partner->isActive(),
                'isSelfFunded' => $partner->isSelfFunded(),
                'isCoordinator' => $partner->isCoordinator(),
                'technicalContact' => $this->contactProvider->generateArray($partner->getTechnicalContact()),
                'organisation' => $this->organisationProvider->generateArray($partner->getOrganisation()),
                'latestVersionCosts' => number_format($partner->getLatestVersionCosts(), 2),
                'latestVersionEffort' => number_format($partner->getLatestVersionEffort(), 2),
            ];
            $this->cache->setItem($cacheKey, $partnerData);
        }

        return $partnerData;
    }

//    public function generateYearArray($partner, $year): array
//    {
//        $cacheKey    = sprintf('%s-%d', $partner->getResourceId(), $year);
//        $partnerData = $this->cache->getItem($cacheKey);
//
//        if (!$partnerData) {
//            /** @var CostsAndEffort $costsAndEffort */
//            foreach ($partner->getCostsAndEffort() as $costsAndEffort) {
//                $partnerData = [
//                    'id'                        => $partner->getId(),
//                    'slug'                      => $partner->getSlug(),
//                    'project'                   => $this->projectProvider->generateArray($partner->getProject()),
//                    'isActive'                  => $partner->isActive(),
//                    'isSelfFunded'              => $partner->isSelfFunded(),
//                    'isCoordinator'             => $partner->isCoordinator(),
//                    'technicalContact'          => $this->contactProvider->generateArray(
//                        $partner->getTechnicalContact()
//                    ),
//                    'organisation'              => $this->organisationProvider->generateArray(
//                        $partner->getOrganisation()
//                    ),
//                    'latestVersionCosts'        => number_format($partner->getLatestVersionCosts(), 2),
//                    'latestVersionEffort'       => number_format($partner->getLatestVersionEffort(), 2),
//                    'year'                      => $costsAndEffort->getYear(),
//                    'latestVersionCostsInYear'  => $costsAndEffort->getCosts(),
//                    'latestVersionEffortInYear' => $costsAndEffort->getEffort(),
//                ];
//            }
//
//            $this->cache->setItem($cacheKey, $partnerData);
//        }
//
//        return $partnerData;
//    }

}
