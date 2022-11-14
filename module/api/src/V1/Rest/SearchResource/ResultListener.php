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
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;

final class ResultListener extends AbstractResourceListener
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly OrganisationService $organisationService,
        private readonly UserService $userService,
        private readonly SearchResultProvider $searchResultProvider
    ) {
    }

    public function fetchAll($params = []): Paginator
    {
        $query = $this->getEvent()->getQueryParam(name: 'query');

        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        if (null === $user) {
            return new Paginator(adapter: new ArrayAdapter());
        }

        $results = [];

        $projects = $this->projectService->searchProjects(
            user: $user,
            query: $query,
            limit: 20
        );

        foreach ($projects as $resultArray) {
            $project = $resultArray[0];
            $score = isset($resultArray['score']) ? (float)$resultArray['score'] : null;

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
            limit: 20
        );

        foreach ($organisations as $resultArray) {
            $organisation = $resultArray[0];
            $score = isset($resultArray['score']) ? (float)$resultArray['score'] : null;

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
