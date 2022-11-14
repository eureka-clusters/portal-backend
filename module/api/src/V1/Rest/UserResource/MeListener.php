<?php

declare(strict_types=1);

namespace Api\V1\Rest\UserResource;

use Admin\Provider\UserProvider;
use Admin\Service\UserService;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;

final class MeListener extends AbstractResourceListener
{
    public function __construct(private readonly UserService $userService, private readonly UserProvider $userProvider)
    {
    }

    public function fetch($id)
    {
        $user = $this->userService->findUserById(id: (int) $this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user) {
            return new ApiProblem(status: 404, detail: 'The selected user cannot be found');
        }

        return $this->userProvider->generateArray(user: $user);
    }
}
