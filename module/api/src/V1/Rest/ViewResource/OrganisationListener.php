<?php

declare(strict_types=1);

namespace Api\V1\Rest\ViewResource;

use Cluster\Provider\OrganisationProvider;
use Cluster\Service\OrganisationService;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use OpenApi\Attributes as OA;

final class OrganisationListener extends AbstractResourceListener
{
    public function __construct(
        private readonly OrganisationService $organisationService,
        private readonly OrganisationProvider $organisationProvider
    ) {
    }

    #[OA\Get(
        path: '/api/view/organisation/{slug}',
        description: 'Organisation information',
        summary: 'Get details from an organisation',
        tags: ['Organisation'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                description: 'Organisation slug',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'organisation-slug'
            ),
        ],
        responses: [
            new OA\Response(ref: '#/components/responses/organisation', response: 200),
            new OA\Response(
                response: 400,
                description: 'The selected organisation cannot be found'
            ),
            new OA\Response(response: 403, description: 'Forbidden'),
        ],
    )]
    public function fetch($id = null): array|ApiProblem
    {
        $organisation = $this->organisationService->findOrganisationBySlug(slug: $id);

        if (null === $organisation) {
            return new ApiProblem(status: 400, detail: 'The selected organisation cannot be found');
        }

        return $this->organisationProvider->generateArray(entity: $organisation);
    }
}
