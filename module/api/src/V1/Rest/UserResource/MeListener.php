<?php

declare(strict_types=1);

namespace Api\V1\Rest\UserResource;

use Admin\Provider\UserProvider;
use Admin\Service\UserService;
use Api\Listener\AbstractRoutedListener;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use OpenApi\Attributes as OA;

final class MeListener extends AbstractRoutedListener
{
    protected static string $route = '/api/[:id]';

    public function __construct(private readonly UserService $userService, private readonly UserProvider $userProvider)
    {
    }

    #[OA\Get(
        path: '/api/me',
        description: 'User information',
        summary: 'Get details from the current user',
        tags: ['User'],
        responses: [
            new OA\Response(ref: '#/components/responses/user', response: 200),
            new OA\Response(response: 403, description: 'Forbidden'),
        ],
    )]
    #[\Override]
    public function fetch($id): array|ApiProblem
    {
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        if (!$user instanceof \Admin\Entity\User) {
            return new ApiProblem(status: 400, detail: 'The selected user cannot be found');
        }

        return $this->userProvider->generateArray(entity: $user);
    }
}
