<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Admin\Entity\User;
use Application\Entity\AbstractEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Table(name: 'oauth_refresh_tokens')]
#[ORM\Entity]
class RefreshToken extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'refresh_token', unique: true)]
    private string $refreshToken;

    #[ORM\ManyToOne(targetEntity: Client::class, cascade: ['persist'], inversedBy: 'refreshTokens')]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'client_id', nullable: false)]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'oAuthRefreshTokens')]
    #[ORM\JoinColumn(name: 'user_id', nullable: true)]
    private ?User $user = null;

    #[ORM\Column(name: 'expires', type: 'datetime_immutable')]
    private DateTimeImmutable $expires;

    #[ORM\Column(name: 'scope', length: 2000, nullable: true)]
    private ?string $scope = null;

    #[Pure] public function __construct()
    {
        $this->client = new Client();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): RefreshToken
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

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): RefreshToken
    {
        $this->client = $client;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): RefreshToken
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
