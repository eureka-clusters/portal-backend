<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Admin\Entity\User;
use Application\Entity\AbstractEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'oauth_authorization_codes')]
#[ORM\Entity]
class AuthorizationCode extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'authorization_code', type: 'string', length: 255, unique: true)]
    private string $authorizationCode = '';

    #[ORM\ManyToOne(targetEntity: Client::class, cascade: ['persist'], inversedBy: 'authorizationCodes')]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'client_id', nullable: false)]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'oAuthAuthorizationCodes')]
    #[ORM\JoinColumn(name: 'user_id', nullable: true)]
    private ?User $user = null;

    #[ORM\Column(name: 'expires', type: 'datetime_immutable')]
    private DateTimeImmutable $expires;

    #[ORM\Column(name: 'redirect_uri', type: 'string', length: 2000)]
    private string $redirectUri = '';

    #[ORM\Column(name: 'scope', type: 'string', length: 2000, nullable: true)]
    private ?string $scope = null;

    #[ORM\Column(name: 'id_token', type: 'string', length: 2000, nullable: true)]
    private ?string $idToken = null;

    public function __construct()
    {
        $this->client = new Client();
        $this->expires = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): AuthorizationCode
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

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): AuthorizationCode
    {
        $this->client = $client;
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

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): AuthorizationCode
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
