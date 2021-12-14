<?php

declare(strict_types=1);

namespace Api\V1\Rest\UserResource;

use Admin\Provider\UserProvider;
use Admin\Service\UserService;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;

final class MeListener extends AbstractResourceListener
{
    public function __construct(private UserService $userService, private UserProvider $userProvider)
    {
    }

    public function fetch($id)
    {
        $user = $this->userService->findUserById((int) $this->getIdentity()?->getName());

        if (null === $user) {
            return new ApiProblem(404, 'The selected user cannot be found');
        }

        return $this->userProvider->generateArray($user);
    }
}
