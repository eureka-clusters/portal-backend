<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Facets;

use Admin\Service\UserService;
use Api\Listener\AbstractRoutedListener;
use Cluster\Service\Project\PartnerService;
use Jield\Search\ValueObject\SearchFormResult;
use Laminas\Json\Json;
use OpenApi\Attributes as OA;
use function base64_decode;

final class PartnerListener extends AbstractRoutedListener
{
    protected static string $route = '/api/statistics/facets/partner/:filter';

    public function __construct(
        private readonly PartnerService $partnerService,
        private readonly UserService    $userService
    )
    {
    }

    #[OA\Get(
        path: '/api/statistics/facets/partner/{filter}',
        description: 'Project partner facets',
        summary: 'Get array with project partner facets, based on the filter',
        tags: ['Project'],
        parameters: [
            new OA\Parameter(
                name: 'filter',
                description: 'base64 encoded JSON filter',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'eyJ0eXBlIjoiY29udGFjdCIsImNvbnRhY3QiOlt7Im5hbWUiOiJwcm9qZWN0IiwidmFsdWUiOjF9XX0='
            ),
        ],
        responses: [
            new OA\Response(ref: '#/components/responses/partner_facets', response: 200),
            new OA\Response(response: 403, description: 'Forbidden'),
        ],
    )]
    #[\Override]
    public function fetch($id): array
    {
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        $filter = [];

        if (!empty($id)) {
            $encodedFilter    = base64_decode((string)$id, true);
            $filter['filter'] = Json::decode(encodedValue: $encodedFilter, objectDecodeType: Json::TYPE_ARRAY);
        }

        $searchFormResult = SearchFormResult::fromArray($filter);

        return $this->partnerService->generateFacets(user: $user, searchFormResult: $searchFormResult);
    }
}
