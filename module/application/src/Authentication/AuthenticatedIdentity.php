<?php

namespace Application\Authentication;

use Admin\Entity\User;
use JetBrains\PhpStorm\Pure;
use Laminas\ApiTools\MvcAuth\Identity\AuthenticatedIdentity as BaseIdentity;

class AuthenticatedIdentity extends BaseIdentity
{
    private User $user;

    #[Pure] public function __construct($userId)
    {
        parent::__construct(['user_id' => $userId]);
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
