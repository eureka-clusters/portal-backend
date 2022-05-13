<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Application\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Table(name: 'oauth_public_keys')]
#[ORM\Entity]
class PublicKey extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\OneToOne(inversedBy: 'publicKey', targetEntity: Client::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'client_id', referencedColumnName: 'client_id', nullable: false)]
    private Client $client;

    #[ORM\Column(name: 'public_key', type: 'text')]
    private string $publicKey = '';

    #[ORM\Column(name: 'private_key', type: 'text')]
    private string $privateKey = '';

    #[ORM\Column(name: 'encryption_algorithm', type: 'string')]
    private string $encryptionAlgorithm = '';

    #[Pure] public function __construct()
    {
        $this->client = new Client();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): PublicKey
    {
        $this->id = $id;
        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): PublicKey
    {
        $this->client = $client;
        return $this;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function setPublicKey(string $publicKey): PublicKey
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function setPrivateKey(string $privateKey): PublicKey
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    public function getEncryptionAlgorithm(): string
    {
        return $this->encryptionAlgorithm;
    }

    public function setEncryptionAlgorithm(string $encryptionAlgorithm): PublicKey
    {
        $this->encryptionAlgorithm = $encryptionAlgorithm;
        return $this;
    }
}
