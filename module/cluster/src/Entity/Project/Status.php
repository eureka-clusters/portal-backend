<?php

declare(strict_types=1);

namespace Cluster\Entity\Project;

use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="cluster_project_status")
 * @ORM\Entity
 */
class Status extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /** @ORM\Column(unique=true) */
    private string $status;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project", cascade={"persist"}, mappedBy="status")
     */
    private Collection $projects;

    #[Pure] public function __construct()
    {
        $this->projects = new ArrayCollection();
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

    public function getProjects(): ArrayCollection|Collection
    {
        return $this->projects;
    }

    public function setProjects($projects): static
    {
        $this->projects = $projects;
        return $this;
    }
}
