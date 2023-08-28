<?php

declare(strict_types=1);

namespace Cluster\Provider\Project;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Partner;
use Cluster\Entity\Version\Type;
use Cluster\Provider\ContactProvider;
use Cluster\Provider\OrganisationProvider;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\Project\PartnerService;
use Cluster\Service\Project\VersionService;
use Laminas\Cache\Storage\Adapter\Redis;
use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'project_partner',
    description: 'Project partner information',
    content: new OA\JsonContent(ref: '#/components/schemas/project_partner')
)]
class PartnerProvider implements ProviderInterface
{
    public function __construct(
        private readonly Redis                $cache,
        private readonly PartnerService       $partnerService,
        private readonly VersionService       $versionService,
        private readonly ProjectProvider      $projectProvider,
        private readonly ContactProvider      $contactProvider,
        private readonly OrganisationProvider $organisationProvider
    )
    {
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
                property: 'projectOutlineCosts',
                description: 'Costs in Project Outline (in EUR)',
                type: 'float',
                example: 1_000_000
            ),
            new OA\Property(
                property: 'projectOutlineEffort',
                description: 'Effort in Project Outline (in PY)',
                type: 'float',
                example: 34.42
            ),
            new OA\Property(
                property: 'fullProjectProposalCosts',
                description: 'Costs in Full Project Proposal (in EUR)',
                type: 'float',
                example: 2_304_000
            ),
            new OA\Property(
                property: 'fullProjectProposalEffort',
                description: 'Effort in Full Project Proposal (in PY)',
                type: 'float',
                example: 54.85
            ),
            new OA\Property(
                property: 'latestVersionCosts',
                description: 'Latest version costs (in EUR)',
                type: 'float',
                example: 3_000_000
            ),
            new OA\Property(
                property: 'latestVersionEffort',
                description: 'Latest version effort (in PY)',
                type: 'float',
                example: 123.42
            ),
        ]
    )]
    public function generateArray($entity): array
    {
        /** @var Partner $partner */
        $partner = $entity;

        $cacheKey    = $partner->parseCacheKey();
        $partnerData = $this->cache->getItem(key: $cacheKey);

        //Get the project outline
        $projectOutline      = $this->versionService->findVersionTypeByProjectAndVersionTypeName(project: $partner->getProject(), versionTypeName: Type::TYPE_PO);
        $fullProjectProposal = $this->versionService->findVersionTypeByProjectAndVersionTypeName(project: $partner->getProject(), versionTypeName: Type::TYPE_FPP);
        $latestVersion       = $this->versionService->findVersionTypeByProjectAndVersionTypeName(project: $partner->getProject(), versionTypeName: Type::TYPE_LATEST);

        if (!$partnerData) {

            $projectOutlineCosts       = null === $projectOutline ? null : $this->partnerService->findTotalCostsByPartnerAndProjectVersion(partner: $partner, projectVersion: $projectOutline);
            $projectOutlineEffort      = null === $projectOutline ? null : $this->partnerService->findTotalEffortByPartnerAndProjectVersion(partner: $partner, projectVersion: $projectOutline);
            $fullProjectProposalCosts  = $this->partnerService->findTotalCostsByPartnerAndProjectVersion(partner: $partner, projectVersion: $fullProjectProposal);
            $fullProjectProposalEffort = $this->partnerService->findTotalEffortByPartnerAndProjectVersion(partner: $partner, projectVersion: $fullProjectProposal);
            $latestVersionCosts        = $this->partnerService->findTotalCostsByPartnerAndProjectVersion(partner: $partner, projectVersion: $latestVersion);
            $latestVersionEffort       = $this->partnerService->findTotalEffortByPartnerAndProjectVersion(partner: $partner, projectVersion: $latestVersion);

            $partnerData = [
                'id'                        => $partner->getId(),
                'slug'                      => $partner->getSlug(),
                'project'                   => $this->projectProvider->generateArray(entity: $partner->getProject()),
                'isActive'                  => $partner->isActive(),
                'isSelfFunded'              => $partner->isSelfFunded(),
                'isCoordinator'             => $partner->isCoordinator(),
                'technicalContact'          => $this->contactProvider->generateArray(
                    entity: $partner->getTechnicalContact()
                ),
                'organisation'              => $this->organisationProvider->generateArray(
                    entity: $partner->getOrganisation()
                ),
                'projectOutlineCosts'       => $projectOutlineCosts,
                'projectOutlineEffort'      => $projectOutlineEffort,
                'fullProjectProposalCosts'  => $fullProjectProposalCosts,
                'fullProjectProposalEffort' => $fullProjectProposalEffort,
                'latestVersionCosts'        => $latestVersionCosts,
                'latestVersionEffort'       => $latestVersionEffort,
            ];
            $this->cache->setItem(key: $cacheKey, value: $partnerData);
        }

        return $partnerData;
    }
}
