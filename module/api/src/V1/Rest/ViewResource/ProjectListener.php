<?php

declare(strict_types=1);

namespace Api\V1\Rest\ViewResource;

use Cluster\Provider\ProjectProvider;
use Cluster\Service\ProjectService;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;

final class ProjectListener extends AbstractResourceListener
{
    private ProjectService $projectService;
    private ProjectProvider $projectProvider;

    public function __construct(ProjectService $projectService, ProjectProvider $projectProvider)
    {
        $this->projectService  = $projectService;
        $this->projectProvider = $projectProvider;
    }

    public function fetch($slug = null)
    {
        $project = $this->projectService->findProjectBySlug($slug);

        if (null === $project) {
            return new ApiProblem(404, 'The selected project cannot be found');
        }

        return $this->projectProvider->generateArray($project);
    }
}
