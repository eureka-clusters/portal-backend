<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Table(name: 'oauth_scopes')]
#[ORM\Entity(repositoryClass: \Api\Repository\OAuth\Scope::class)]
class Scope extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'type', type: 'string')]
    private string $type = 'supported';

    #[ORM\Column(name: 'scope', type: 'string', length: 2000)]
    private string $scope = '';

    #[ORM\OneToMany(mappedBy: 'scope', targetEntity: Client::class, cascade: ['persist'])]
    private Collection $clients;

    #[ORM\Column(name: 'is_default', type: 'boolean')]
    private bool $isDefault = true;

    #[Pure] public function __construct()
    {
        $this->clients = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->scope;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Scope
    {
        $this->id = $id;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Scope
    {
        $this->type = $type;
        return $this;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function setScope(string $scope): Scope
    {
        $this->scope = $scope;
        return $this;
    }

    public function getClients(): Collection|array
    {
        return $this->clients;
    }

    public function setClients(Collection|array $clients): Scope
    {
        $this->clients = $clients;
        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): Scope
    {
        $this->isDefault = $isDefault;
        return $this;
    }
}
