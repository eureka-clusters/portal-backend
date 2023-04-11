<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Partner;
use Cluster\Provider\ContactProvider;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\ProjectProvider;
use Laminas\Cache\Storage\Adapter\Redis;
use OpenApi\Attributes as OA;

use function number_format;

#[OA\Response(
    response: 'project_partner',
    description: 'Project partner information',
    content: new OA\JsonContent(ref: '#/components/schemas/project_partner')
)]
class PartnerProvider implements ProviderInterface
{
    public function __construct(
        private readonly Redis $cache,
        private readonly ProjectProvider $projectProvider,
        private readonly ContactProvider $contactProvider,
        private readonly OrganisationProvider $organisationProvider
    ) {
    }

    #[OA\Schema(
        schema: 'project_partner',
        title: 'Project partner',
        description: 'Information about a project partner',
        properties: [
            new OA\Property(
                property: 'id',
                description: 'Partner ID',
                type: 'integer',
                example: 1
            ),
            new OA\Property(
                property: 'slug',
                description: 'Partner slug',
                type: 'string',
                example: 'partner-1'
            ),
            new OA\Property(
                property: 'project',
                ref: '#/components/schemas/project',
                description: 'Project information'
            ),
            new OA\Property(
                property: 'isActive',
                description: 'Is active',
                type: 'boolean',
                example: true
            ),
            new OA\Property(
                property: 'isSelfFunded',
                description: 'Is self funded',
                type: 'boolean',
                example: false
            ),
            new OA\Property(
                property: 'isCoordinator',
                description: 'Is coordinator',
                type: 'boolean',
                example: false
            ),
            new OA\Property(
                property: 'technicalContact',
                ref: '#/components/schemas/contact',
                description: 'Technical contact information'
            ),
            new OA\Property(
                property: 'organisation',
                ref: '#/components/schemas/organisation',
                description: 'Organisation information'
            ),
            new OA\Property(
                property: 'latestVersionCosts',
                description: 'Latest version costs',
                type: 'string',
                example: '100.00'
            ),
            new OA\Property(
                property: 'latestVersionEffort',
                description: 'Latest version effort',
                type: 'string',
                example: '200.00'
            ),
        ]
    )]
    public function generateArray($entity): array
    {
        /** @var Partner $partner */
        $partner = $entity;

        $cacheKey    = $partner->getResourceId();
        $partnerData = $this->cache->getItem(key: $cacheKey);

        if (!$partnerData) {
            $partnerData = [
                'id'                  => $partner->getId(),
                'slug'                => $partner->getSlug(),
                'project'             => $this->projectProvider->generateArray(entity: $partner->getProject()),
                'isActive'            => $partner->isActive(),
                'isSelfFunded'        => $partner->isSelfFunded(),
                'isCoordinator'       => $partner->isCoordinator(),
                'technicalContact'    => $this->contactProvider->generateArray(
                    entity: $partner->getTechnicalContact()
                ),
                'organisation'        => $this->organisationProvider->generateArray(
                    entity: $partner->getOrganisation()
                ),
                'latestVersionCosts'  => number_format(num: $partner->getLatestVersionCosts(), decimals: 2),
                'latestVersionEffort' => number_format(num: $partner->getLatestVersionEffort(), decimals: 2),
            ];
            $this->cache->setItem(key: $cacheKey, value: $partnerData);
        }

        return $partnerData;
    }
}
