<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Api\Paginator\DoctrineORMAdapter;
use Cluster\Provider\OrganisationProvider;
use Cluster\Service\OrganisationService;
use Jield\Search\ValueObject\SearchFormResult;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;
use Laminas\Paginator\Paginator;
use OpenApi\Attributes as OA;

final class OrganisationListener extends AbstractResourceListener
{
    public function __construct(
        private readonly OrganisationService $organisationService,
        private readonly OrganisationProvider $organisationProvider
    ) {
    }

    #[OA\Get(
        path: '/api/list/organisation',
        description: 'List of organisations',
        summary: 'Get a list of organisations',
        tags: ['Organisation'],
        parameters: [
            new OA\Parameter(
                name: 'filter',
                description: 'Base64 encoded JSON filter',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: null
            ),
            new OA\Parameter(
                name: 'query',
                description: 'Search Query',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: null
            ),
            new OA\Parameter(
                name: 'order',
                description: 'Sort order',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'name'
            ),
            new OA\Parameter(
                name: 'direction',
                description: 'Sort direction',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'asc'
            ),
            new OA\Parameter(
                name: 'pageSize',
                description: 'Amount per page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 25
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(ref: '#/components/responses/organisation', response: 200),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 400, description: 'Project or partner not found'),
        ],
    )]
    public function fetchAll($params = []): Paginator
    {
        $filter = $params->toArray();

        //Inject the encoded filter from the results
        $filter['filter'] = [];
        if (!empty($params->filter)) {
            $encodedFilter    = base64_decode($params->filter, true);
            $filter['filter'] = Json::decode(encodedValue: $encodedFilter, objectDecodeType: Json::TYPE_ARRAY);
        }

        $searchFormResult = SearchFormResult::fromArray($filter);

        $organisationQueryBuilder = $this->organisationService->getOrganisations(searchFormResult: $searchFormResult);

        $doctrineORMAdapter = new DoctrineORMAdapter(query: $organisationQueryBuilder);
        $doctrineORMAdapter->setProvider(provider: $this->organisationProvider);

        return new Paginator(adapter: $doctrineORMAdapter);
    }
}
