<?php

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Stringable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="oauth_scopes")
 * @ORM\Entity(repositoryClass="Api\Repository\OAuth\Scope")
 */
class Scope extends AbstractEntity implements Stringable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;
    /**
     * @ORM\Column(name="type", type="string")
     */
    private string $type = 'supported';
    /**
     * @ORM\Column(name="scope", length=2000, type="string")
     */
    private string $scope = '';
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\Client", mappedBy="scope", cascade={"persist"})
     */
    private Collection|array $clients;
    /**
     * @ORM\Column(name="is_default", type="boolean")
     */
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
