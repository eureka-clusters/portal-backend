<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Version\CostsAndEffort;
use Cluster\Provider\ContactProvider;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\ProjectProvider;
use Laminas\Cache\Storage\Adapter\Redis;

use function sprintf;

class PartnerYearProvider implements ProviderInterface
{
    public function __construct(
        private Redis $cache,
        private ProjectProvider $projectProvider,
        private ContactProvider $contactProvider,
        private OrganisationProvider $organisationProvider
    ) {
    }

    public function generateArray($entity): array
    {
        $partner = $entity;

        $cacheKey    = sprintf('%s-years', $partner->getResourceId());
        $partnerData = $this->cache->getItem($cacheKey);

        if (true || !$partnerData) {
            /** @var CostsAndEffort $costsAndEffort */
            foreach ($partner->getCostsAndEffort() as $costsAndEffort) {
                $partnerData = [
                    'id'                        => $partner->getId(),
                    'slug'                      => $partner->getSlug(),
                    'project'                   => $this->projectProvider->generateArray($partner->getProject()),
                    'isActive'                  => $partner->isActive(),
                    'isSelfFunded'              => $partner->isSelfFunded(),
                    'isCoordinator'             => $partner->isCoordinator(),
                    'technicalContact'          => $this->contactProvider->generateArray(
                        $partner->getTechnicalContact()
                    ),
                    'organisation'              => $this->organisationProvider->generateArray(
                        $partner->getOrganisation()
                    ),
                    'latestVersionCosts'        => number_format($partner->getLatestVersionCosts(), 2),
                    'latestVersionEffort'       => number_format($partner->getLatestVersionEffort(), 2),
                    'year'                      => $costsAndEffort->getYear(),
                    'latestVersionCostsInYear'  => number_format($costsAndEffort->getCosts(), 2),
                    'latestVersionEffortInYear' => number_format($costsAndEffort->getEffort(), 2),
                ];
            }

            $this->cache->setItem($cacheKey, $partnerData);
        }

        return $partnerData;
    }

}
