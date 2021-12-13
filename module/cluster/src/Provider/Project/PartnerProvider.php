<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Cluster\Entity;
use Cluster\Provider\ContactProvider;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\Project\PartnerService;
use Doctrine\Common\Cache\RedisCache;
use InvalidArgumentException;

use JetBrains\PhpStorm\ArrayShape;

use function sprintf;

class PartnerProvider
{
    public function __construct(
        private RedisCache $redisCache,
        private ProjectProvider $projectProvider,
        private ContactProvider $contactProvider,
        private OrganisationProvider $organisationProvider,
        private PartnerService $partnerService
    ) {
    }

    #[ArrayShape(['id'               => "int",
                  'organisation'     => "string",
                  'isActive'         => "bool",
                  'isSelfFunded'     => "bool",
                  'isCoordinator'    => "bool",
                  'technicalContact' => "array"
    ])] public static function parseCoordinatorArray(Entity\Project\Partner $partner): array
    {
        if (! $partner->isCoordinator()) {
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

    public function generateArray(Entity\Project\Partner $partner): array
    {
        $cacheKey    = $partner->getResourceId();
        $partnerData = $this->redisCache->fetch($cacheKey);

        if (! $partnerData) {
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

            $this->redisCache->save($cacheKey, $partnerData);
        }

        return $partnerData;
    }
}
