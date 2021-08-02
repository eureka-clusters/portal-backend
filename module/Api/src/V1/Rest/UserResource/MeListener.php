<?php

/**
 * Jield BV all rights reserved
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2020 Jield BV (https://jield.nl)
 */

namespace Api\V1\Rest\UserResource;

use Admin\Provider\UserProvider;
use Admin\Service\UserService;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 * Class MeListener
 * @package Api\V1\Rest\UserResource
 */
final class MeListener extends AbstractResourceListener
{
    private UserService  $userService;
    private UserProvider $userProvider;

    public function __construct(UserService $userService, UserProvider $userProvider)
    {
        $this->userService  = $userService;
        $this->userProvider = $userProvider;
    }

    public function fetch($id)
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()->getAuthenticationIdentity()['user_id']);

        if (null === $user) {
            return new ApiProblem(404, 'The selected user cannot be found');
        }

        return $this->userProvider->generateArray($user);
    }
}
