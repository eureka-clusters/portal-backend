<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Application\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Table(name: 'oauth_jwt')]
#[ORM\Entity]
class Jwt extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Client::class, cascade: ['persist'], inversedBy: 'jwtTokens')]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'client_id', nullable: false)]
    private Client $client;

    #[ORM\Column(name: 'public_key', length: 2000)]
    private string $publicKey = '';

    #[ORM\Column(name: 'subject')]
    private string $subject = '';

    #[Pure] public function __construct()
    {
        $this->client = new Client();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Jwt
    {
        $this->id = $id;
        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): Jwt
    {
        $this->client = $client;
        return $this;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function setPublicKey(string $publicKey): Jwt
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): Jwt
    {
        $this->subject = $subject;
        return $this;
    }
}
