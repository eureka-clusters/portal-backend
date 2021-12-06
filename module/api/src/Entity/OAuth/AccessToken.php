<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Admin\Entity\User;
use Application\Entity\AbstractEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oauth_access_tokens")
 * @ORM\Entity
 */
class AccessToken extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer",nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /** @ORM\Column(name="access_token", length=255, type="string",unique=true) */
    private string $accessToken;
    /** @ORM\Column(name="client_id", length=255, type="string", nullable=false) */
    private string $clientId;
    /**
     * @ORM\ManyToOne(targetEntity="Admin\Entity\User", cascade={"persist"}, inversedBy="oAuthAccessTokens")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;
    /** @ORM\Column(name="expires", type="datetime_immutable") */
    private DateTimeImmutable $expires;
    /** @ORM\Column(length=2000, nullable=true) */
    private ?string $scope;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): AccessToken
    {
        $this->id = $id;
        return $this;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): AccessToken
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): AccessToken
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): AccessToken
    {
        $this->user = $user;
        return $this;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }

    public function setExpires(DateTimeImmutable $expires): AccessToken
    {
        $this->expires = $expires;
        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): AccessToken
    {
        $this->scope = $scope;
        return $this;
    }
}
