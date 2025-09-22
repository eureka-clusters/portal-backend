<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Admin\Entity\User;
use Admin\Service\UserService;
use Api\Listener\AbstractRoutedListener;
use Api\Paginator\DoctrineORMAdapter;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ProjectService;
use Jield\Search\ValueObject\SearchFormResult;
use Laminas\Json\Json;
use Laminas\Paginator\Paginator;
use Laminas\Stdlib\Parameters;
use OpenApi\Attributes as OA;
use Override;

final class ProjectListener extends AbstractRoutedListener
{
    protected static string $route = '/api/list/project';

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
                name:        'filter',
                description: 'Base64 encoded JSON filter',
                in:          'query',
                required:    false,
                schema:      new OA\Schema(type: 'string'),
                example:     null
            ),
            new OA\Parameter(
                name:        'query',
                description: 'Search Query',
                in:          'query',
                required:    false,
                schema:      new OA\Schema(type: 'string'),
                example:     null
            ),
            new OA\Parameter(
                name:        'order',
                description: 'Sort order',
                in:          'query',
                required:    false,
                schema:      new OA\Schema(type: 'string'),
                example:     'name'
            ),
            new OA\Parameter(
                name:        'direction',
                description: 'Sort direction',
                in:          'query',
                required:    false,
                schema:      new OA\Schema(type: 'string'),
                example:     'asc'
            ),
            new OA\Parameter(
                name:        'pageSize',
                description: 'Amount per page',
                in:          'query',
                required:    false,
                schema:      new OA\Schema(type: 'integer'),
                example:     25
            ),
            new OA\Parameter(
                name:        'page',
                description: 'Page',
                in:          'query',
                required:    false,
                schema:      new OA\Schema(type: 'integer'),
                example:     1
            ),
        ],
        responses: [
            new OA\Response(ref: '#/components/responses/project', response: 200),
            new OA\Response(response: 403, description: 'Forbidden'),
        ],
    )]
    #[Override]
    public function fetchAll(Parameters $params): Paginator
    {
        /** @var User $user */
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        $filter = $params->toArray();

        //Inject the encoded filter from the results
        $filter['filter'] = [];
        if (null !== $params->get(name: 'filter')) {
            $encodedFilter    = base64_decode(string: (string)$params->get(name: 'filter'), strict: true);
            $filter['filter'] = Json::decode(encodedValue: $encodedFilter, objectDecodeType: Json::TYPE_ARRAY);
        }

        $searchFormResult = SearchFormResult::fromArray(params: $filter);

        $projectQueryBuilder = $this->projectService->getProjects(
            user:             $user,
            searchFormResult: $searchFormResult,
        );

        $doctrineORMAdapter = new DoctrineORMAdapter(query: $projectQueryBuilder);
        $doctrineORMAdapter->setProvider(provider: $this->projectProvider);

        return new Paginator(adapter: $doctrineORMAdapter);
    }
}
