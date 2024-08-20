<?php

declare(strict_types=1);

namespace Application\Authentication\Adapter;

use PDO;

use function array_merge;
use function sprintf;

final class PdoAdapter extends \Laminas\ApiTools\OAuth2\Adapter\PdoAdapter
{
    #[\Override]
    public function getUser($username): array|bool
    {
        $stmt = $this->db->prepare(query: $sql = 'SELECT * from admin_user where email=:email');
        $stmt->execute(params: ['email' => $username]);

        if (! $userInfo = $stmt->fetch(mode: PDO::FETCH_ASSOC)) {
            return false;
        }

        // the default behavior is to use "id" as the user_id
        return array_merge(['user_id' => $userInfo['id']], $userInfo);
    }

    #[\Override]
    protected function checkPassword($user, $password): bool
    {
        return $this->getBcrypt()->verify(password: $password, hash: $user['password']);
    }
}
