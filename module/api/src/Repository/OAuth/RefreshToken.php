<?php

declare(strict_types=1);

namespace Api\Repository\OAuth;

use Doctrine\ORM\EntityRepository;

final class RefreshToken extends EntityRepository //implements RefreshTokenInterface
{
//    public function getRefreshToken($refreshToken)
//    {
//        $token = $this->findOneBy(['refreshToken' => $refreshToken]);
//        if (null !== $token) {
//            $token = $token->toArray();
//            $token['expires'] = $token['expires']->getTimestamp();
//        }
//        return $token;
//    }

//    public function setRefreshToken($refreshToken, $client_id, $user_id, $expires, $scope = null): OAuthRefreshToken
//    {
//        // 1623851087
//        echo 'expires';
//        var_dump($expires);
//
//        echo 'refreshToken';
//        var_dump($refreshToken);
//
//        echo 'user_id';
//        var_dump($user_id);
//
//        // check if client exists
//        $client = $this->_em->getRepository(Clients::class)
//            ->findOneBy(['clientId' => $client_id]);
//
//        if ($client === null) {
//            throw new \Exception("Error Processing Request client doesn't exists", 1);
//        }
//
//        echo 'client';
//        var_dump($client);
//
//        // check if user exists
//        $user = $this->_em->getRepository(User::class)
//        ->findOneBy(['id' => $user_id]);
//
//        if ($user === null
//        ) {
//            throw new \Exception("Error Processing Request user doesn't exists", 1);
//        }
//
//        // should i use the setter function or can i set the attributes via additional function? e.g. fromArray
//        // @Johan what do you prefer?
//        // perhaps there is another function to set multiple attributes?
//
//        // set manually
//        $token = new OAuthRefreshToken();
//        $token->setRefreshToken($refreshToken);
//        $token->setClientId($client->getClientId());
//        $token->setUser($user);
//        $token->setExpires((new \DateTimeImmutable())->setTimestamp($expires));
//        $token->setScope($scope);
//
//        // by function
//        // $token = OAuthRefreshToken::fromArray([
//        //     'refreshToken'     => $refreshToken,
//        //     'clientId' => $client->getClientId(),
//        //     //'client'    => $client,
//        //     'user'      => $user,
//        //     'expires'     => (new \DateTimeImmutable())->setTimestamp($expires),
//        //     //'expires'   => (new \DateTime())->setTimestamp($expires),
//        //     'scope'     => $scope,
//        // ]);
//
//
//        $this->_em->persist($token);
//        $this->_em->flush();
//
//        echo 'token in setRefreshToken';
//        var_dump($token);
//        return $token;
//    }
//
//
//    public function unsetRefreshToken($refresh_token)
//    {
//        // todo
//    }
}
