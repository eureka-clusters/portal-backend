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
        private ProjectService $projectService,
        private UserService $userService,
        private ProjectProvider $projectProvider
    ) {
    }

    public function fetchAll($params = []): Paginator
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return new Paginator(new ArrayAdapter());
        }

        $projectQueryBuilder = $this->projectService->getProjects($user->getFunder(), []);

        $doctrineORMAdapter = new DoctrineORMAdapter($projectQueryBuilder);
        $doctrineORMAdapter->setProvider($this->projectProvider);

        return new Paginator($doctrineORMAdapter);
    }
}
