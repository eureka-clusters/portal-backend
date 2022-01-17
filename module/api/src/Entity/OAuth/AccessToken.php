<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Admin\Entity\User;
use Application\Entity\AbstractEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Table(name: 'oauth_access_tokens')]
#[ORM\Entity]
class AccessToken extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(name: 'access_token', type: 'text')]
    private string $accessToken = '';

    #[ORM\ManyToOne(targetEntity: Client::class, cascade: ['persist'], inversedBy: 'accessTokens')]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'client_id', nullable: false, columnDefinition: 'varchar(255)')]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'oAuthAccessTokens')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(name: 'expires', type: 'datetime_immutable')]
    private DateTimeImmutable $expires;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $scope = null;

    #[Pure] public function __construct()
    {
        $this->client = new Client();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): AccessToken
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

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): AccessToken
    {
        $this->client = $client;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): AccessToken
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
