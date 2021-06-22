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
use Api\Entity\OAuth\Clients;

/**
 * @ORM\Table(name="oauth_access_tokens")
 * @ORM\Entity(repositoryClass="Api\Repository\OAuth\AccessToken")
 */
class AccessToken extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer",nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    private int $id;
    /**
     * @ORM\Column(name="access_token", length=255, type="string",unique=true)
     */
    private string $accessToken;
    /**
     * @ORM\ManyToOne(targetEntity="Api\Entity\OAuth\Clients", cascade={"persist"}, inversedBy="oAuthAccessTokens")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="client_id", nullable=false)
     */
    private Clients $oAuthClient;
    /**
     * @ORM\ManyToOne(targetEntity="Admin\Entity\User", cascade={"persist"}, inversedBy="oAuthAccessTokens")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private ?User $user;
    /**
     * @ORM\Column(name="expires", type="datetime_immutable")
     */
    private DateTimeImmutable $expires;
    /**
     * @ORM\Column(name="scope", length=2000, type="string", nullable=false)
     */
    private ?string $scope;

    public function getId(): int
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

    public function getOAuthClient(): ?\Api\Entity\OAuth\Clients
    {
        return $this->oAuthClient;
    }

    public function setOAuthClient(?\Api\Entity\OAuth\Clients $oAuthClient): AccessToken
    {
        $this->oAuthClient = $oAuthClient;
        return $this;
    }

    public function getUser(): ?User
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
