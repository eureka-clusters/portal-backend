<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Application\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oauth_scopes")
 * @ORM\Entity(repositoryClass="Api\Repository\OAuth\Scopes")
 */
class Scopes extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;
    /** @ORM\Column(name="type", type="string") */
    private string $type = 'supported';
    /** @ORM\Column(name="scope", length=2000, type="string") */
    private ?string $scope;
    /** @ORM\Column(name="client_id", type="string", ) */
    private string $clientId;
    /** @ORM\Column(name="is_default", type="smallint", ) */
    private ?int $isDefault;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Scopes
    {
        $this->id = $id;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Scopes
    {
        $this->type = $type;
        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): Scopes
    {
        $this->scope = $scope;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): Scopes
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getIsDefault(): ?int
    {
        return $this->isDefault;
    }

    public function setIsDefault(?int $isDefault): Scopes
    {
        $this->isDefault = $isDefault;
        return $this;
    }
}
