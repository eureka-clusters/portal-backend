<?php

declare(strict_types=1);

namespace Api\Service;

use Admin\Entity\User;
use Api\Entity\OAuth;
use Application\Service\AbstractService;
use OAuth2\Encryption\Jwt;

class OAuthService extends AbstractService
{
    public function findOrGenereateJWTToken(User $user): OAuth\Jwt
    {
        $client = $this->findDefaultJWTClient();

        //Try to find an existing JWT
        $jwt = $this->entityManager->getRepository(OAuth\Jwt::class)->findOneBy(
            [
                'client' => $client,
                'user'   => $user
            ]
        );

        //Return token, or create new one if not found
        return $jwt ?? $this->createJWTToken($user);
    }

    public function findDefaultJWTClient(): OAuth\Client
    {
        $client = $this->entityManager->getRepository(OAuth\Client::class)->findOneBy(
            [
                'isJwt' => true,
            ]
        );

        if (null === $client) {
            throw new \RuntimeException("No default JWT client created");
        }

        return $client;
    }

    public function createJWTToken(User $user): OAuth\Jwt
    {
        $client = $this->findDefaultJWTClient();

        $jwt = new OAuth\Jwt();
        $jwt->setUser($user);
        $jwt->setClient($client);

        //We have the user now, so lets create a JWT for this guy
        $payload = [
            'id' => $user->getId()
        ];

        $token = (new Jwt())->encode($payload, $client->getJwtKey());
        $jwt->setToken($token);

        $this->save($jwt);

        return $jwt;
    }
}
