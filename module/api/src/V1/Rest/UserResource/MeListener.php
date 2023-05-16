<?php

declare(strict_types=1);

namespace Api\V1\Rest\UserResource;

use Admin\Provider\UserProvider;
use Admin\Service\UserService;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use OpenApi\Attributes as OA;

final class MeListener extends AbstractResourceListener
{
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
    public function fetch($id): array|ApiProblem
    {
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        if (null === $user) {
            return new ApiProblem(status: 400, detail: 'The selected user cannot be found');
        }

        return $this->userProvider->generateArray(entity: $user);
    }
}
