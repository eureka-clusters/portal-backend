<?php

namespace Application\Authentication;

use Admin\Entity\User;
use Laminas\ApiTools\MvcAuth\Identity\AuthenticatedIdentity as BaseIdentity;

class AuthenticatedIdentity extends BaseIdentity
{
    private User $user;

    public function __construct($userId)
    {
        parent::__construct(['user_id' => $userId]);
//        $this->setName($user->getEmail());
//        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
