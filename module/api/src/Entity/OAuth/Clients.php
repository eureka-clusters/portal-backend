<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Admin\Entity\User;
use Api\Entity\OAuth\AccessToken;
use Api\Entity\OAuth\AuthorizationCode;
use Api\Entity\OAuth\RefreshToken;
use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oauth_clients")
 * @ORM\Entity(repositoryClass="Api\Repository\OAuth\Clients")
 */
class Clients extends AbstractEntity
{
    /**
     * @ORM\Column(name="client_id", length=255, type="string",unique=true)
     * @ORM\Id
     */
    private string $clientId;
    /** @ORM\Column() */
    private string $name;
    /** @ORM\Column(name="text") */
    private string $description;
    /** @ORM\Column(name="client_secret", length=255, type="string") */
    private string $clientSecret;
    /** @ORM\Column(name="redirect_uri", length=2000, type="string", nullable=true) */
    private ?string $redirectUri = null;
    /** @ORM\Column(name="grant_types", length=2000, type="string", nullable=true) */
    private ?string $grantTypes;
    /** @ORM\Column(name="scope", length=2000, type="string") */
    private string $scope;
    /**
     * @ORM\ManyToOne(targetEntity="Admin\Entity\User", cascade={"persist"}, inversedBy="oAuthClients")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private ?User $user = null;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\AccessToken", cascade={"persist"}, mappedBy="oAuthClient")
     *
     * @var AccessToken[]|ArrayCollection
     */
    private $oAuthAccessTokens;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\RefreshToken", cascade={"persist"}, mappedBy="oAuthClient")
     *
     * @var RefreshToken[]|ArrayCollection
     */
    private $oAuthRefreshTokens;

    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\AuthorizationCode", cascade={"persist"}, mappedBy="oAuthClient")
     *
     * @var AuthorizationCode[]|ArrayCollection
     */
    private $oAuthAuthorizationCodes;

    public function __construct()
    {
        $this->oAuthAccessTokens       = new ArrayCollection();
        $this->oAuthRefreshTokens      = new ArrayCollection();
        $this->oAuthAuthorizationCodes = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->clientId;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): Clients
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): Clients
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function setRedirectUri(string $redirectUri): Clients
    {
        $this->redirectUri = $redirectUri;
        return $this;
    }

    public function getGrantTypes(): ?string
    {
        return $this->grantTypes;
    }

    public function setGrantTypes(?string $grantTypes): Clients
    {
        $this->grantTypes = $grantTypes;
        return $this;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function setScope(string $scope): Clients
    {
        $this->scope = $scope;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): Clients
    {
        $this->user = $user;
        return $this;
    }

    public function getOAuthAccessTokens(): ArrayCollection
    {
        return $this->oAuthAccessTokens;
    }

    public function setOAuthAccessTokens($oAuthAccessTokens): Clients
    {
        $this->oAuthAccessTokens = $oAuthAccessTokens;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Clients
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Clients
    {
        $this->description = $description;
        return $this;
    }

    public function getOAuthRefreshTokens()
    {
        return $this->oAuthRefreshTokens;
    }

    public function setOAuthRefreshTokens($oAuthRefreshTokens): Clients
    {
        $this->oAuthRefreshTokens = $oAuthRefreshTokens;
        return $this;
    }

    public function getOAuthAuthorizationCodes()
    {
        return $this->oAuthAuthorizationCodes;
    }

    public function setOAuthAuthorizationCodes($oAuthAuthorizationCodes): Clients
    {
        $this->oAuthAuthorizationCodes = $oAuthAuthorizationCodes;
        return $this;
    }
}
