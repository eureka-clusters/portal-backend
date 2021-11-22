<?php

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
    /** @ORM\Column(name="refresh_token", length=255, type="string", unique=true) */
    private string $refreshToken;
    /**
     * @ORM\ManyToOne(targetEntity="Api\Entity\OAuth\Clients", cascade={"persist"}, inversedBy="oAuthRefreshTokens")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="client_id", nullable=false)
     */
    private Clients $oAuthClient;
    /**
     * @ORM\ManyToOne(targetEntity="Admin\Entity\User", cascade={"persist"}, inversedBy="oAuthRefreshTokens")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", )
     */
    private User $user;
    /** @ORM\Column(name="expires", type="datetime_immutable") */
    private DateTimeImmutable $expires;
    /** @ORM\Column(name="scope", length=2000, type="string") */
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

    public function getOAuthClient(): ?Clients
    {
        return $this->oAuthClient;
    }

    public function setOAuthClient(?Clients $oAuthClient): RefreshToken
    {
        $this->oAuthClient = $oAuthClient;
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