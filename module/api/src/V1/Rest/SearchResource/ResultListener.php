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

        //$user = $this->userService->findUserById((int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);
        $user = $this->userService->findUserById((int)10);

        if (null === $user || !$user->isFunder()) {
            return new Paginator(new ArrayAdapter());
        }

        $results = [];

        $projects = $this->projectService->searchProjects(funder: $user->getFunder(), query: $query, limit: 5);

        /** @var Project $project */
        foreach ($projects as $project) {
            $results[] = new SearchResult(
                type:        'project',
                slug:        $project->getSlug(),
                name:        $project->getName(),
                title:       $project->getTitle(),
                description: $project->getDescription()
            );
        }

        $organisations = $this->organisationService->searchOrganisations(
            funder: $user->getFunder(),
            query:  $query,
            limit:  5
        );

        /** @var Organisation $organisation */
        foreach ($organisations as $organisation) {
            $results[] = new SearchResult(
                type:             'organisation',
                slug:             $organisation->getSlug(),
                name:             $organisation->getName(),
                organisationType: $organisation->getType()->getType(),
                country:          $organisation->getCountry()->getCountry()
            );
        }

        $doctrineORMAdapter = new CustomAdapter($results);
        $doctrineORMAdapter->setProvider($this->searchResultProvider);

        return new Paginator($doctrineORMAdapter);
    }
}
