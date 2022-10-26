<?php

declare(strict_types=1);

namespace Application\Authentication\Adapter;

use Admin\Entity\User;
use Admin\Service\UserService;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;

final class DatabaseAdapter implements AdapterInterface
{
    public function __construct(
        private readonly UserService $userService,
        private readonly string $username,
        private readonly string $password
    ) {
    }

    public function authenticate(): Result
    {
        $user = $this->userService->findUserByEmail($this->username);

        if (null === $user) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['A record with the supplied identity could not be found.']
            );
        }

        if (!$this->validateCredential($user, $this->password)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Supplied credential is not valid']);
        }

        return new Result(Result::SUCCESS, $user, ['Authentication successful']);
    }

    private function validateCredential(User $user, $credential): bool
    {
        $bcrypt = new Bcrypt();
        $bcrypt->setCost(14);

        return $bcrypt->verify((string)$credential, $user->getPassword());
    }
}
