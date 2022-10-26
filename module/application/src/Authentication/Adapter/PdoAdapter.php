<?php

declare(strict_types=1);

namespace Application\Authentication\Adapter;

use PDO;

final class PdoAdapter extends \Laminas\ApiTools\OAuth2\Adapter\PdoAdapter
{
    public function getUser($username): array|bool
    {
        $stmt = $this->db->prepare(query: $sql = sprintf(format: 'SELECT * from admin_user where email=:email'));
        $stmt->execute(params: ['email' => $username]);

        if (!$userInfo = $stmt->fetch(mode: PDO::FETCH_ASSOC)) {
            return false;
        }

        // the default behavior is to use "id" as the user_id
        return array_merge(['user_id' => $userInfo['id']], $userInfo);
    }

    protected function checkPassword($user, $password): bool
    {
        return $this->getBcrypt()->verify(password: $password, hash: $user['password']);
    }
}
