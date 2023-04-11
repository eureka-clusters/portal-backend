<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Admin\Service\UserService;
use Api\Paginator\DoctrineORMAdapter;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ProjectService;
use Jield\Search\ValueObject\SearchFormResult;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;
use Laminas\Paginator\Paginator;
use OpenApi\Attributes as OA;

final class ProjectListener extends AbstractResourceListener
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly UserService $userService,
        private readonly ProjectProvider $projectProvider
    ) {
    }

    #[OA\Get(
        path: '/api/list/project',
        description: 'List of projects',
        summary: 'Get a list of projects',
        tags: ['Project'],
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
            new OA\Response(ref: '#/components/responses/project', response: 200),
            new OA\Response(response: 403, description: 'Forbidden'),
        ],
    )]
    public function fetchAll($params = []): Paginator
    {
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        $filter = $params->toArray();

        //Inject the encoded filter from the results
        if (isset($params->filter)) {
            $filter           = base64_decode(string: $params->filter, strict: true);
            $filter['filter'] = Json::decode(encodedValue: $filter, objectDecodeType: Json::TYPE_ARRAY);
        }

        $searchFormResult = SearchFormResult::fromArray($filter);

        $projectQueryBuilder = $this->projectService->getProjects(
            user: $user,
            searchFormResult: $searchFormResult,
        );

        $doctrineORMAdapter = new DoctrineORMAdapter(query: $projectQueryBuilder);
        $doctrineORMAdapter->setProvider(provider: $this->projectProvider);

        return new Paginator(adapter: $doctrineORMAdapter);
    }
}
