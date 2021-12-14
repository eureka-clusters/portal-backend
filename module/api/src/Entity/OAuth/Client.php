<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="oauth_clients")
 * @ORM\Entity(repositoryClass="Api\Repository\OAuth\Client")
 */
class Client extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer",nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /**
     * @ORM\Column(name="client_id", length=255, type="string",unique=true)
     */
    private string $clientId;
    /**
     * @ORM\Column(name="client_secret", length=255, type="string")
     */
    private string $clientsecret;
    /**
     * @ORM\Column(length=2000, nullable=true)
     */
    private string $jwtKey;
    /**
     * @ORM\Column(name="client_secret_teaser", length=255, type="string")
     */
    private string $clientsecretTeaser;
    /**
     * @ORM\Column(name="redirect_uri", length=2000, type="string")
     */
    private string $redirectUri;
    /**
     * @ORM\Column(name="grant_types", length=2000, type="string", nullable=true)
     */
    private ?string $grantTypes = null;
    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isJwt = false;
    /**
     * @ORM\ManyToOne(targetEntity="Api\Entity\OAuth\Scope", cascade={"persist"}, inversedBy="clients")
     * @ORM\JoinColumn(nullable=false)
     */
    private Scope $scope;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\Jwt", mappedBy="client", cascade={"persist"})
     */
    private array|Collection $jwt;

    #[Pure] public function __construct()
    {
        $this->jwt = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Client
    {
        $this->id = $id;
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

    public function getJwt(): Collection|array
    {
        return $this->jwt;
    }

    public function setJwt(Collection|array $jwt): Client
    {
        $this->jwt = $jwt;
        return $this;
    }

    public function getJwtKey(): string
    {
        return $this->jwtKey;
    }

    public function setJwtKey(string $jwtKey): Client
    {
        $this->jwtKey = $jwtKey;
        return $this;
    }

    public function isJwt(): bool
    {
        return $this->isJwt;
    }

    public function setIsJwt(bool $isJwt): Client
    {
        $this->isJwt = $isJwt;
        return $this;
    }
}
