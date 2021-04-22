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
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oauth_clients")
 * @ORM\Entity(repositoryClass="Api\Repository\OAuth\Clients")
 */
class Clients extends AbstractEntity
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
    private string $clientSecret;
    /**
     * @ORM\Column(name="redirect_uri", length=2000, type="string")
     */
    private string $redirectUri;
    /**
     * @ORM\Column(name="grant_types", length=2000, type="string", )
     */
    private ?string $grantTypes;
    /**
     * @ORM\Column(name="scope", length=2000, type="string")
     */
    private string $scope;
    /**
     * @ORM\ManyToOne(targetEntity="Admin\Entity\User", cascade={"persist"}, inversedBy="oAuthClients")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Clients
    {
        $this->id = $id;
        return $this;
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Clients
    {
        $this->user = $user;
        return $this;
    }
}
