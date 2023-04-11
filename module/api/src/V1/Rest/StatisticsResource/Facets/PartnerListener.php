<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Facets;

use Admin\Service\UserService;
use Cluster\Service\Project\PartnerService;
use Jield\Search\ValueObject\SearchFormResult;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;
use OpenApi\Attributes as OA;

use function base64_decode;

final class PartnerListener extends AbstractResourceListener
{
    public function __construct(
        private readonly PartnerService $partnerService,
        private readonly UserService $userService
    ) {
    }

    #[OA\Get(
        path: '/api/statistics/facets/partner',
        description: 'Project partner facets',
        summary: 'Get array with project partner facets, based on the filter',
        tags: ['Project'],
        parameters: [
            new OA\Parameter(
                name: 'filter',
                description: 'base64 encoded JSON filter',
                in: 'query',
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
    public function fetchAll($params = []): array
    {
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        $filter = $params->toArray();

        //Inject the encoded filter from the results
        if (isset($params->filter)) {
            $encodedFilter    = base64_decode(string: $params->filter, strict: true);
            $filter['filter'] = Json::decode(encodedValue: $encodedFilter, objectDecodeType: Json::TYPE_ARRAY);
        }

        $searchFormResult = SearchFormResult::fromArray($filter);

        return $this->partnerService->generateFacets(user: $user, searchFormResult: $searchFormResult);
    }
}
