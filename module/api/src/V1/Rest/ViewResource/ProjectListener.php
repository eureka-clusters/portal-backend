<?php

declare(strict_types=1);

namespace Api\V1\Rest\ViewResource;

use Admin\Service\UserService;
use Api\Listener\AbstractRoutedListener;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ProjectService;
use Jield\ApiTools\ApiProblem\ApiProblem;
use OpenApi\Attributes as OA;

final class ProjectListener extends AbstractRoutedListener
{
    protected static string $route = '/api/view/project/:id';

    public function __construct(
        private readonly ProjectService  $projectService,
        private readonly UserService     $userService,
        private readonly ProjectProvider $projectProvider
    )
    {
    }

    #[OA\Get(
        path: '/api/view/project/{slug}',
        description: 'Project information',
        summary: 'Get details from a project',
        tags: ['Project'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                description: 'Project slug',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'project-slug'
            ),
        ],
        responses: [
            new OA\Response(ref: '#/components/responses/project', response: 200),
            new OA\Response(
                response: 400,
                description: 'Project could not be found or you have no access to this project'
            ),
            new OA\Response(response: 403, description: 'Forbidden'),
        ],
    )]
    #[\Override]
    public function fetch($id = null): array|ApiProblem
    {
        $slug = $id;

        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        $project = $this->projectService->findProjectBySlugAndUser(slug: $slug, user: $user);

        if (!$project instanceof \Cluster\Entity\Project) {
            return new ApiProblem(
                status: 400,
                detail: 'Project could not be found or you have no access to this project'
            );
        }

        if (!$project->isOnWebsite()) {
            return new ApiProblem(
                status: 403,
                detail: 'Project is not published'
            );
        }

        return $this->projectProvider->generateArray(entity: $project);
    }
}
