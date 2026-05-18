<?php

declare(strict_types=1);

namespace Api\V1\Rest\ViewResource;

use Api\Listener\AbstractRoutedListener;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Service\Project\PartnerService;
use Jield\ApiTools\ApiProblem\ApiProblem;
use OpenApi\Attributes as OA;

final class PartnerListener extends AbstractRoutedListener
{
    protected static string $route = '/api/view/partner/:id';

    public function __construct(
        private readonly PartnerService  $partnerService,
        private readonly PartnerProvider $partnerProvider
    )
    {
    }

    #[OA\Get(
        path: '/api/view/partner/{slug}',
        description: 'Project partner information',
        summary: 'Get details from a project partner',
        tags: ['Project'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                description: 'Partner slug',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'partner-slug'
            ),
        ],
        responses: [
            new OA\Response(ref: '#/components/responses/project_partner', response: 200),
            new OA\Response(response: 400, description: 'The selected project partner cannot be found'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ],
    )]
    #[\Override]
    public function fetch($id = null): array|ApiProblem
    {
        $partner = $this->partnerService->findPartnerBySlug(slug: $id);

        if (!$partner instanceof \Cluster\Entity\Project\Partner) {
            return new ApiProblem(status: 400, detail: 'The selected project partner cannot be found');
        }

        if (!$partner->getProject()->isOnWebsite()) {
            return new ApiProblem(
                status: 403,
                detail: 'Partner is not published'
            );
        }

        return $this->partnerProvider->generateArray(entity: $partner);
    }
}
