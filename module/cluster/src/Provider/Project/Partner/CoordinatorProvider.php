<?php

declare(strict_types=1);

namespace Cluster\Provider\Project\Partner;

use Api\Provider\ProviderInterface;
use Cluster\Entity\Project\Partner;
use Cluster\Provider\ContactProvider;
use InvalidArgumentException;
use OpenApi\Attributes as OA;

use function sprintf;

#[OA\Response(
    response: 'project_partner_coordinator',
    description: 'Project coordinator information',
    content: new OA\JsonContent(ref: '#/components/schemas/project_partner_coordinator')
)]
class CoordinatorProvider implements ProviderInterface
{
    public function __construct(
        private readonly ContactProvider $contactProvider,
    ) {
    }

    #[OA\Schema(
        schema: 'project_partner_coordinator',
        title: 'Project coordinator',
        description: 'Information about a project coordinator',
        properties: [
            new OA\Property(
                property: 'id',
                description: 'Partner ID',
                type: 'integer',
                example: 1
            ),
            new OA\Property(
                property: 'organisation',
                description: 'Organisation name',
                type: 'string',
                example: 'Example organisation'
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
                example: true
            ),
            new OA\Property(
                property: 'technicalContact',
                ref: '#/components/schemas/contact',
                description: 'Technical contact information'
            ),
        ]
    )]
    public function generateArray($entity): array
    {
        /** @var Partner $partner */
        $partner = $entity;

        if (!$partner->isCoordinator()) {
            throw new InvalidArgumentException(
                message: sprintf("%s in %s is no coordinator", $partner->getOrganisation(), $partner->getProject())
            );
        }

        return [
            'id'               => $partner->getId(),
            'organisation'     => $partner->getOrganisation()->getName(),
            'isActive'         => $partner->isActive(),
            'isSelfFunded'     => $partner->isSelfFunded(),
            'isCoordinator'    => $partner->isCoordinator(),
            'technicalContact' => $this->contactProvider->generateArray($partner->getTechnicalContact()),
        ];
    }
}
