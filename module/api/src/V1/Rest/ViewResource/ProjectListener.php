<?php

declare(strict_types=1);

namespace Api\V1\Rest\ViewResource;

use Admin\Service\UserService;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;

final class ProjectListener extends AbstractResourceListener
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly UserService $userService,
        private readonly ProjectProvider $projectProvider
    ) {
    }

    public function fetch($id = null)
    {
        $slug = $id;

        $user = $this->userService->findUserById(id: (int) $this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user) {
            return new ApiProblem(status: 404, detail: 'The selected user cannot be found');
        }

        $project = $this->projectService->findProjectBySlugAndUser(slug: $slug, user: $user);

        if (null === $project) {
            return new ApiProblem(status: 404, detail: 'The selected project cannot be found');
        }

        return $this->projectProvider->generateArray(project: $project);
    }
}
