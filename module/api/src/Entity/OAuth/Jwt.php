<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Admin\Entity\User;
use Application\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oauth_jwt")
 * @ORM\Entity
 */
class Jwt extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer",nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /**
     * @ORM\ManyToOne(targetEntity="Api\Entity\OAuth\Client", cascade={"persist"}, inversedBy="jwt")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Client $client = null;
    /**
     * @ORM\ManyToOne(targetEntity="Admin\Entity\User", cascade={"persist"}, inversedBy="oAuthJwt")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;
    /**
     * @ORM\Column()
     */
    private string $token;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Jwt
    {
        $this->id = $id;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): Jwt
    {
        $this->client = $client;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Jwt
    {
        $this->user = $user;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): Jwt
    {
        $this->token = $token;
        return $this;
    }
}
