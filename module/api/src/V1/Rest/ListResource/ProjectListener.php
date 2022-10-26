<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Admin\Service\UserService;
use Api\Paginator\DoctrineORMAdapter;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;

final class ProjectListener extends AbstractResourceListener
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly UserService $userService,
        private readonly ProjectProvider $projectProvider
    ) {
    }

    public function fetchAll($params = []): Paginator
    {
        $user = $this->userService->findUserById(id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user) {
            return new Paginator(adapter: new ArrayAdapter());
        }

        $defaultSort = 'project.name';

        $sort = $this->getEvent()->getQueryParams()?->get(name: 'sort', default: $defaultSort);
        $order = $this->getEvent()->getQueryParams()?->get(name: 'order', default: 'asc');

        $projectQueryBuilder = $this->projectService->getProjects(user: $user, filter: [], sort: $sort, order: $order);

        $doctrineORMAdapter = new DoctrineORMAdapter(query: $projectQueryBuilder);
        $doctrineORMAdapter->setProvider(provider: $this->projectProvider);

        return new Paginator(adapter: $doctrineORMAdapter);
    }
}
