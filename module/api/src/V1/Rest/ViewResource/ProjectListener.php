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

    public function fetch($slug = null)
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return new ApiProblem(404, 'The selected project cannot be found');
        }

        // $project = $this->projectService->findProjectBySlug($slug);
        $project = $this->projectService->findProjectBySlugAndFunder($slug, $user->getFunder());

        if (null === $project) {
            return new ApiProblem(404, 'The selected project cannot be found');
        }

        return $this->projectProvider->generateArray($project);
    }
}
