<?php

declare(strict_types=1);

namespace Api\V1\Rest\SearchResource;

use Admin\Service\UserService;
use Api\Paginator\CustomAdapter;
use Application\ValueObject\SearchResult;
use Cluster\Entity\Organisation;
use Cluster\Entity\Project;
use Cluster\Provider\SearchResultProvider;
use Cluster\Service\OrganisationService;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;

final class ResultListener extends AbstractResourceListener
{
    public function __construct(
        private ProjectService $projectService,
        private OrganisationService $organisationService,
        private UserService $userService,
        private SearchResultProvider $searchResultProvider
    ) {
    }

    public function fetchAll($params = []): Paginator
    {
        $query = $this->getEvent()->getQueryParam('query');

        $user = $this->userService->findUserById((int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return new Paginator(new ArrayAdapter());
        }

        // $test = $this->projectService->searchTest(
        //     funder: $user->getFunder(),
        //     query: $query,
        //     limit: 20
        // );
        // return $test;

        $results = [];

        $projects = $this->projectService->searchProjects(
            funder: $user->getFunder(),
            query: $query,
            limit: 20
        );

        /** @var Project $project */
        // foreach ($projects as $project) {
        foreach ($projects as $resultArray) {
            $project = $resultArray[0];
            $score = $resultArray['score'] ?? null;

            $results[] = new SearchResult(
                type:        'project',
                slug:        $project->getSlug(),
                name:        $project->getName(),
                title:       $project->getTitle(),
                description: $project->getDescription(),
                score:       $score
            );
        }

        $organisations = $this->organisationService->searchOrganisations(
            funder: $user->getFunder(),
            query:  $query,
            limit:  20
        );

        /** @var Organisation $organisation */
        // foreach ($organisations as $organisation) {
        foreach ($organisations as $resultArray) {
            $organisation = $resultArray[0];
            $score = $resultArray['score'] ?? null;

            $results[] = new SearchResult(
                type:             'organisation',
                slug:             $organisation->getSlug(),
                name:             $organisation->getName(),
                organisationType: $organisation->getType()->getType(),
                country:          $organisation->getCountry()->getCountry(),
                score:            $score
            );
        }

        // if results would be a normal array i would have sorted it after the score.
        // $score = array_column($results, 'score');
        // array_multisort($score, SORT_DESC, $results);

        $doctrineORMAdapter = new CustomAdapter($results);
        $doctrineORMAdapter->setProvider($this->searchResultProvider);

        return new Paginator($doctrineORMAdapter);
    }
}
