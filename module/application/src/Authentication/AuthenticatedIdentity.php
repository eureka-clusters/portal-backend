<?php

namespace Application\Authentication;

use Admin\Entity\User;
use Laminas\ApiTools\MvcAuth\Identity\AuthenticatedIdentity as BaseIdentity;

class AuthenticatedIdentity extends BaseIdentity
{
    private User $user;

    public function __construct(User $user)
    {
        parent::__construct($user->getEmail());
        $this->setName($user->getEmail());
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
