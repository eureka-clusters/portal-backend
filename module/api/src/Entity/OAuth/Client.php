<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Table(name: 'oauth_clients')]
#[ORM\Entity(repositoryClass: \Api\Repository\OAuth\Client::class)]
class Client extends AbstractEntity
{
    #[ORM\Column(name: 'client_id', unique: true)]
    #[ORM\Id]
    private ?string $clientId = null;

    #[ORM\Column(name: 'client_secret')]
    private string $clientsecret;

    #[ORM\Column(name: 'name')]
    private string $name = '';

    #[ORM\Column(name: 'description', type: 'text')]
    private string $description = '';

    #[ORM\Column(name: 'client_secret_teaser')]
    private string $clientsecretTeaser = '';

    #[ORM\Column(name: 'redirect_uri', length: 2000)]
    private string $redirectUri;

    #[ORM\Column(name: 'grant_types', length: 2000, nullable: true)]
    private ?string $grantTypes = null;

    #[ORM\ManyToOne(targetEntity: Scope::class, cascade: ['persist'], inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: false)]
    private Scope $scope;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Jwt::class, cascade: ['persist'])]
    private Collection $jwtTokens;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: AccessToken::class, cascade: ['persist'])]
    private Collection $accessTokens;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: AuthorizationCode::class, cascade: ['persist'])]
    private Collection $authorizationCodes;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: RefreshToken::class, cascade: ['persist'])]
    private Collection $refreshTokens;

    #[ORM\OneToOne(mappedBy: 'client', targetEntity: PublicKey::class, cascade: ['persist'])]
    private ?PublicKey $publicKey = null; //Cannot initiate here so nullable is needed

    #[Pure] public function __construct()
    {
        $this->jwtTokens = new ArrayCollection();
        $this->accessTokens = new ArrayCollection();
        $this->authorizationCodes = new ArrayCollection();
        $this->refreshTokens = new ArrayCollection();

        $this->scope = new Scope();
    }

    public function getId(): ?string
    {
        return $this->clientId;
    }

    public function setId(string|int $clientId): Client
    {
        $this->setClientId((string)$clientId);

        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): Client
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getClientsecret(): string
    {
        return $this->clientsecret;
    }

    public function setClientsecret(string $clientsecret): Client
    {
        $this->clientsecret = $clientsecret;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Client
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Client
    {
        $this->description = $description;
        return $this;
    }

    public function getClientsecretTeaser(): string
    {
        return $this->clientsecretTeaser;
    }

    public function setClientsecretTeaser(string $clientsecretTeaser): Client
    {
        $this->clientsecretTeaser = $clientsecretTeaser;
        return $this;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function setRedirectUri(string $redirectUri): Client
    {
        $this->redirectUri = $redirectUri;
        return $this;
    }

    public function getGrantTypes(): ?string
    {
        return $this->grantTypes;
    }

    public function setGrantTypes(?string $grantTypes): Client
    {
        $this->grantTypes = $grantTypes;
        return $this;
    }

    public function getScope(): Scope
    {
        return $this->scope;
    }

    public function setScope(Scope $scope): Client
    {
        $this->scope = $scope;
        return $this;
    }

    public function getJwtTokens(): ArrayCollection|Collection
    {
        return $this->jwtTokens;
    }

    public function setJwtTokens(ArrayCollection|Collection $jwtTokens): Client
    {
        $this->jwtTokens = $jwtTokens;
        return $this;
    }

    public function getAccessTokens(): ArrayCollection|Collection
    {
        return $this->accessTokens;
    }

    public function setAccessTokens(ArrayCollection|Collection $accessTokens): Client
    {
        $this->accessTokens = $accessTokens;
        return $this;
    }

    public function getAuthorizationCodes(): ArrayCollection|Collection
    {
        return $this->authorizationCodes;
    }

    public function setAuthorizationCodes(ArrayCollection|Collection $authorizationCodes): Client
    {
        $this->authorizationCodes = $authorizationCodes;
        return $this;
    }

    public function getRefreshTokens(): ArrayCollection|Collection
    {
        return $this->refreshTokens;
    }

    public function setRefreshTokens(ArrayCollection|Collection $refreshTokens): Client
    {
        $this->refreshTokens = $refreshTokens;
        return $this;
    }

    public function getPublicKey(): ?PublicKey
    {
        return $this->publicKey;
    }

    public function setPublicKey(?PublicKey $publicKey): Client
    {
        $this->publicKey = $publicKey;
        return $this;
    }
}
