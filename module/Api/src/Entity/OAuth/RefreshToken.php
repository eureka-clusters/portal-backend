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
 * @ORM\Table(name="oauth_refresh_tokens")
 * @ORM\Entity(repositoryClass="Api\Repository\OAuth\RefreshToken")
 */
class RefreshToken extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer",nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
    /**
     * @ORM\Column(name="refresh_token", length=255, type="string", unique=true)
     */
    private string $refreshToken;
    /**
     * @ORM\Column(name="client_id", type="string")
     */
    private string $clientId;
    /**
     * @ORM\ManyToOne(targetEntity="Admin\Entity\User", cascade={"persist"}, inversedBy="oAuthRefreshTokens")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", )
     */
    private User $user;
    /**
     * @ORM\Column(name="expires", type="datetime_immutable")
     */
    private DateTimeImmutable $expires;
    /**
     * @ORM\Column(name="scope", length=2000, type="string", )
     */
    private ?string $scope;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): RefreshToken
    {
        $this->id = $id;
        return $this;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): RefreshToken
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): RefreshToken
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): RefreshToken
    {
        $this->user = $user;
        return $this;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }

    public function setExpires(DateTimeImmutable $expires): RefreshToken
    {
        $this->expires = $expires;
        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): RefreshToken
    {
        $this->scope = $scope;
        return $this;
    }
}
