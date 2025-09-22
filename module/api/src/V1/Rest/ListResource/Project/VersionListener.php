<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource\Project;

use Admin\Entity\User;
use Admin\Service\UserService;
use Api\Listener\AbstractRoutedListener;
use Api\Paginator\DoctrineORMAdapter;
use Cluster\Entity\Project;
use Cluster\Provider\Project\VersionProvider;
use Cluster\Service\Project\VersionService;
use Cluster\Service\ProjectService;
use Jield\ApiTools\ApiProblem\ApiProblem;
use Jield\Search\ValueObject\SearchFormResult;
use Laminas\Json\Json;
use Laminas\Paginator\Paginator;
use Laminas\Stdlib\Parameters;
use OpenApi\Attributes as OA;

final class VersionListener extends AbstractRoutedListener
{
    protected static string $route = '/api/list/project/version';

    protected static array $entityCollectionWhitelist
        = [
            'project',
        ];

    public function __construct(
        private readonly VersionService $versionService,
        private readonly ProjectService $projectService,
        private readonly UserService $userService,
        private readonly VersionProvider $versionProvider
    ) {
    }

    #[OA\Get(
        path: '/api/list/project/version',
        description: 'List of project versions',
        summary: 'Get a list of project versions',
        tags: ['Project'],
        parameters: [
            new OA\Parameter(
                name:        'project',
                description: 'Project slug to filter by',
                in:          'query',
                required:    false,
                schema:      new OA\Schema(type: 'string'),
                example:     'symfony'
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
    #[\Override]
    public function fetchAll(Parameters $params): Paginator|ApiProblem
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

        $projectVersionQueryBuilder = $this->versionService->getVersions(
            user:             $user,
            searchFormResult: $searchFormResult,
        );

        if (null !== $params->get(name: 'project')) {
            /** @var Project $project */
            $project = $this->projectService->findProjectBySlug(slug: (string)$params->get(name: 'project'));

            if (null === $project) {
                return new ApiProblem(status: 400, detail: 'Project cannot not found');
            }

            $projectVersionQueryBuilder->andWhere(
                $projectVersionQueryBuilder->expr()->eq(
                    x: 'cluster_entity_project.id',
                    y: $project->getId()
                )
            );
        }

        $doctrineORMAdapter = new DoctrineORMAdapter(query: $projectVersionQueryBuilder);
        $doctrineORMAdapter->setProvider(provider: $this->versionProvider);

        return new Paginator(adapter: $doctrineORMAdapter);
    }
}
