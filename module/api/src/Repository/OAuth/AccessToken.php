<?php

declare(strict_types=1);

namespace Api\Repository\OAuth;

use Doctrine\ORM\EntityRepository;

final class AccessToken extends EntityRepository //implements AccessTokenInterface
{
//    public function getAccessToken($oauthToken)
//    {
//        $token = $this->findOneBy(['token' => $oauthToken]);
//        if ($token) {
//            $token = $token->toArray();
//            $token['expires'] = $token['expires']->getTimestamp();
//        }
//        return $token;
//    }

//    public function setAccessToken($oauthToken, $client_id, $user_id, $expires, $scope = null): OAuthAccessToken
//    {
//        // expires is timestamp e.g 1623851087
//
//        echo 'oauthToken';
//        var_dump($oauthToken);
//
//        echo 'user_id';
//        var_dump($user_id);
//
//        // check if the client exists
//        $client = $this->_em->getRepository(Client::class)
//        ->findOneBy(['clientId' => $client_id]);
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
//        if ($user === null) {
//            throw new \Exception("Error Processing Request user doesn't exists", 1);
//        }
//
//        // i couldn't get a "client" relation to work because of the "string" "client_id" which isn't primary key (wrong table layout?)
//        $token = OAuthAccessToken::fromArray([
//            'accessToken'     => $oauthToken,
//            'clientId' => $client->getClientId(),
//            //'client'    => $client,
//            'user'      => $user,
//            'expires'     => (new \DateTimeImmutable())->setTimestamp($expires),
//            //'expires'   => (new \DateTime())->setTimestamp($expires),
//            'scope'     => $scope,
//        ]);
//
//        // save the token
//        $this->_em->persist($token);
//        $this->_em->flush();
//
//        echo 'token in setAccessToken';
//        var_dump($token);
//        return $token;
//    }
}
