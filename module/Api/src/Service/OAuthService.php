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
use Api\Entity\OAuth\AuthorizationCode;
use Api\Entity\OAuth\Clients;
use Api\Options\ModuleOptions;
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
        $authorizationCode = new AuthorizationCode();
        $authorizationCode->setUser($user);
        $authorizationCode->setOAuthClient($oAuthClient);
        $authorizationCode->setScope($oAuthClient->getScope());
        $authorizationCode->setAuthorizationCode($this->generateAuthorizationCode());
        $authorizationCode->setRedirectUri($oAuthClient->getRedirectUri());

        $expireDate = new DateTimeImmutable(
            sprintf('+ %d second', $this->moduleOptions->getAuthorizationCodeLifetime())
        );

        $authorizationCode->setExpires($expireDate);
        $this->save($authorizationCode);

        return $authorizationCode;
    }

    protected function generateAuthorizationCode(): string
    {
        $randomData = Rand::getBytes(500);
        return substr(hash('sha512', $randomData), 0, 40);
    }

    public function findoAuthClientByClientId(string $clientId): Clients
    {
        $client = $this->entityManager->getRepository(Clients::class)->findOneBy(['clientId' => $clientId]);

        if (null === $client) {
            throw new InvalidArgumentException('The selected client cannot be found');
        }

        return $client;
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
