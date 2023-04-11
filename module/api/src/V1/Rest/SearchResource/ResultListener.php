<?php

declare(strict_types=1);

namespace Api\V1\Rest\SearchResource;

use Admin\Service\UserService;
use Api\Paginator\CustomAdapter;
use Application\ValueObject\SearchResult;
use Cluster\Provider\SearchResultProvider;
use Cluster\Service\OrganisationService;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Paginator\Paginator;
use OpenApi\Attributes as OA;

use function usort;

final class ResultListener extends AbstractResourceListener
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly OrganisationService $organisationService,
        private readonly UserService $userService,
        private readonly SearchResultProvider $searchResultProvider
    ) {
    }

    #[OA\Get(
        path: '/api/search/result',
        description: 'Search for projects and organisations',
        summary: 'Get a list of search results',
        tags: ['Project'],
        parameters: [
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
            new OA\Response(ref: '#/components/responses/search_result', response: 200),
            new OA\Response(response: 403, description: 'Forbidden'),
        ],
    )]
    public function fetchAll($params = []): Paginator
    {
        $query = $params->query ?? null;
        $limit = $params->pageSize ?? 25;

        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        $results = [];

        $projects = $this->projectService->searchProjects(
            user: $user,
            query: $query,
            limit: $limit
        );

        foreach ($projects as $resultArray) {
            $project = $resultArray[0];
            $score   = isset($resultArray['score']) ? (float)$resultArray['score'] : null;

            $results[] = new SearchResult(
                type: 'project',
                slug: $project->getSlug(),
                name: $project->getName(),
                title: $project->getTitle(),
                description: $project->getDescription(),
                score: $score
            );
        }

        $organisations = $this->organisationService->searchOrganisations(
            funder: $user->getFunder(),
            query: $query,
            limit: $limit
        );

        foreach ($organisations as $resultArray) {
            $organisation = $resultArray[0];
            $score        = isset($resultArray['score']) ? (float)$resultArray['score'] : null;

            $results[] = new SearchResult(
                type: 'organisation',
                slug: $organisation->getSlug(),
                name: $organisation->getName(),
                organisationType: $organisation->getType()->getType(),
                country: $organisation->getCountry()->getCountry(),
                score: $score
            );
        }

        //Sort on score, but therefore we need to iterate over the scores
        usort(
            array: $results,
            callback: static fn (SearchResult $result1, SearchResult $result2) => $result1->getScore(
            ) < $result2->getScore() ? 1 : -1
        );

        $doctrineORMAdapter = new CustomAdapter(array: $results);
        $doctrineORMAdapter->setProvider(provider: $this->searchResultProvider);

        return new Paginator(adapter: $doctrineORMAdapter);
    }
}
