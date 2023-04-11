<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Partner;
use Cluster\Entity\Project\Version\CostsAndEffort;
use Cluster\Provider\ContactProvider;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\ProjectProvider;
use Laminas\Cache\Storage\Adapter\Redis;

use function number_format;
use function sprintf;

class PartnerYearProvider implements ProviderInterface
{
    private ?int $year = null;

    public function __construct(
        private readonly Redis $cache,
        private readonly ProjectProvider $projectProvider,
        private readonly ContactProvider $contactProvider,
        private readonly OrganisationProvider $organisationProvider
    ) {
    }

    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function generateArray($entity): array
    {
        if (null === $this->year) {
            throw new \RuntimeException('Year is not set');
        }

        /** @var Partner $partner */
        $partner = $entity;

        $cacheKey    = sprintf('%s-year-%d', $partner->getResourceId(), $this->year);
        $partnerData = $this->cache->getItem(key: $cacheKey);

        if (!$partnerData) {
            /** @var CostsAndEffort $costsAndEffort */
            foreach ($partner->getCostsAndEffort() as $costsAndEffort) {
                //Only save the data when the year is the matched year
                if ($costsAndEffort->getYear() === $this->year) {
                    $partnerData = [
                        'id'                        => $partner->getId(),
                        'slug'                      => $partner->getSlug(),
                        'project'                   => $this->projectProvider->generateArray(
                            entity: $partner->getProject()
                        ),
                        'isActive'                  => $partner->isActive(),
                        'isSelfFunded'              => $partner->isSelfFunded(),
                        'isCoordinator'             => $partner->isCoordinator(),
                        'technicalContact'          => $this->contactProvider->generateArray(
                            entity: $partner->getTechnicalContact()
                        ),
                        'organisation'              => $this->organisationProvider->generateArray(
                            entity: $partner->getOrganisation()
                        ),
                        'latestVersionCosts'        => number_format(
                            num: $partner->getLatestVersionCosts(),
                            decimals: 2
                        ),
                        'latestVersionEffort'       => number_format(
                            num: $partner->getLatestVersionEffort(),
                            decimals: 2
                        ),
                        'year'                      => $costsAndEffort->getYear(),
                        'latestVersionCostsInYear'  => number_format(num: $costsAndEffort->getCosts(), decimals: 2),
                        'latestVersionEffortInYear' => number_format(num: $costsAndEffort->getEffort(), decimals: 2),
                    ];
                    $this->cache->setItem(key: $cacheKey, value: $partnerData);
                }
            }
        }

        return $partnerData;
    }
}
