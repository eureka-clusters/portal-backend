<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Admin\Entity\User;
use Application\Entity\AbstractEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oauth_authorization_codes")
 * @ORM\Entity
 */
class AuthorizationCode extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer",nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
    /**
     * @ORM\Column(name="authorization_code", length=255, type="string",unique=true)
     */
    private string $authorizationCode;
    /**
     * @ORM\Column(name="client_id", length=255, type="string")
     */
    private string $clientId;
    /**
     * @ORM\ManyToOne(targetEntity="Admin\Entity\User", cascade={"persist"}, inversedBy="oAuthAuthorizationCodes")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", )
     */
    private ?User $user;
    /**
     * @ORM\Column(name="expires", type="datetime_immutable")
     */
    private DateTimeImmutable $expires;
    /**
     * @ORM\Column(name="redirect_uri", length=2000, type="string")
     */
    private string $redirectUri;
    /**
     * @ORM\Column(name="scope", length=2000, type="string", )
     */
    private ?string $scope;
    /**
     * @ORM\Column(name="id_token", length=2000, type="string", )
     */
    private ?string $idToken;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): AuthorizationCode
    {
        $this->id = $id;
        return $this;
    }

    public function getAuthorizationCode(): string
    {
        return $this->authorizationCode;
    }

    public function setAuthorizationCode(string $authorizationCode): AuthorizationCode
    {
        $this->authorizationCode = $authorizationCode;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): AuthorizationCode
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): AuthorizationCode
    {
        $this->user = $user;
        return $this;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }

    public function setExpires(DateTimeImmutable $expires): AuthorizationCode
    {
        $this->expires = $expires;
        return $this;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function setRedirectUri(string $redirectUri): AuthorizationCode
    {
        $this->redirectUri = $redirectUri;
        return $this;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function setScope(string $scope): AuthorizationCode
    {
        $this->scope = $scope;
        return $this;
    }

    public function getIdToken(): ?string
    {
        return $this->idToken;
    }

    public function setIdToken(?string $idToken): AuthorizationCode
    {
        $this->idToken = $idToken;
        return $this;
    }
}
