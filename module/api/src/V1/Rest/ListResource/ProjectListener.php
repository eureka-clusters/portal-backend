<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Admin\Service\UserService;
use Cluster\Provider\ProjectProvider;
use Cluster\Rest\Collection\ProjectCollection;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

final class ProjectListener extends AbstractResourceListener
{
    private ProjectService $projectService;
    private UserService $userService;
    private ProjectProvider $projectProvider;

    public function __construct(
        ProjectService $projectService,
        UserService $userService,
        ProjectProvider $projectProvider
    ) {
        $this->projectService  = $projectService;
        $this->userService     = $userService;
        $this->projectProvider = $projectProvider;
    }

    public function fetchAll($params = [])
    {
        $user = $this->userService->findUserById((int) $this->getIdentity()->getAuthenticationIdentity());

        if (null === $user || ! $user->isFunder()) {
            return [];
        }

        $projects = $this->projectService->getProjects($user->getFunder(), []);

        return (new ProjectCollection($projects, $this->projectProvider))->getItems(
            $params->offset,
            $params->amount ?? 100
        );
    }
}
