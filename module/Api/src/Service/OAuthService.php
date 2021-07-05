<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Api\Service;

use Admin\Entity\User;
use Api\Entity\OAuth\AccessToken;
use Api\Entity\OAuth\Clients;
use Api\Entity\OAuth\RefreshToken;
use Api\Entity\OAuth\AuthorizationCode;
use Api\Options\ModuleOptions;
use Api\ValueObject\BearerToken;
use Application\Service\AbstractService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Math\Rand;

/**
 * Class AccessToken
 * @package Api\Service
 */
class OAuthService extends AbstractService
{
    private ModuleOptions $moduleOptions;

    public function __construct(
        EntityManager $entityManager,
        TranslatorInterface $translator,
        ModuleOptions $moduleOptions
    ) {
        parent::__construct($entityManager, $translator);
        $this->moduleOptions = $moduleOptions;
    }

    public function createAuthorizationCodeForUser(User $user, Clients $oAuthClient): AuthorizationCode
    {
        $autorizationCode = new AuthorizationCode();
        $autorizationCode->setUser($user);
        $autorizationCode->setOAuthClient($oAuthClient);
        $autorizationCode->setAuthorizationCode($this->generateAuthorizationCode());
        $autorizationCode->setRedirectUri($oAuthClient->getRedirectUri());

        $expireDate = new DateTimeImmutable(sprintf('+ %d second', $this->moduleOptions->getAuthorizationCodeLifetime()));

        $autorizationCode->setExpires($expireDate);
        $this->save($autorizationCode);
        return $autorizationCode;
    }

    public function createTokenForUser(User $user, Clients $oAuthClient): BearerToken
    {
        $accessToken = new AccessToken();
        $accessToken->setUser($user);
        $accessToken->setOAuthClient($oAuthClient);
        $accessToken->setScope($oAuthClient->getScope());
        $accessToken->setAccessToken($this->generateAccessToken());

        $expireDate = new DateTimeImmutable(sprintf('+ %d second', $this->moduleOptions->getAccessTokenLifetime()));

        $accessToken->setExpires($expireDate);
        $this->save($accessToken);

        //Create the refreshToken
        $refreshToken = new RefreshToken();
        $refreshToken->setUser($user);
        $refreshToken->setOAuthClient($oAuthClient);
        $refreshToken->setScope($oAuthClient->getScope());
        $refreshToken->setRefreshToken($this->generateRefreshToken());

        $expireDate = new DateTimeImmutable(sprintf('+ %d second', $this->moduleOptions->getRefreshTokenLifetime()));
        $refreshToken->setExpires($expireDate);
        $this->save($refreshToken);

        return BearerToken::fromArray(
            [
                'accessToken' => $accessToken->getAccessToken(),
                'expiresIn'    => $this->moduleOptions->getAccessTokenLifetime(),
                'tokenType'    => 'Bearer',
                'scope'        => $oAuthClient->getScope(),
                'refreshToken' => $refreshToken->getRefreshToken()
            ]
        );
    }

    public function findoAuthClientByClientId(string $clientId): Clients
    {
        $client = $this->entityManager->getRepository(Clients::class)->findOneBy(['clientId' => $clientId]);

        if (null === $client) {
            throw new InvalidArgumentException('The selected client cannot be found');
        }

        return $client;
    }

    protected function generateAuthorizationCode(): string
    {
        $randomData = Rand::getBytes(500);
        return substr(hash('sha512', $randomData), 0, 40);
    }

    protected function generateAccessToken(): string
    {
        $randomData = Rand::getBytes(500);
        return substr(hash('sha512', $randomData), 0, 40);
    }

    protected function generateRefreshToken(): string
    {
        $randomData = Rand::getBytes(500);
        return substr(hash('sha512', $randomData), 0, 80);
    }
}
