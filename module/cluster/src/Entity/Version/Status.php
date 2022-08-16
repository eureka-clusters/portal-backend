<?php

declare(strict_types=1);

namespace Cluster\Entity\Version;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Project\Version;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Table(name: 'cluster_version_status')]
#[ORM\Entity]
class Status extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private string $status;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Version::class, cascade: ['persist'])]
    private Collection $versions;

    #[Pure] public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Status
    {
        $this->id = $id;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Status
    {
        $this->status = $status;
        return $this;
    }

    public function getVersions(): ArrayCollection|Collection
    {
        return $this->versions;
    }

    public function setVersions($versions): Status
    {
        $this->versions = $versions;
        return $this;
    }
}
