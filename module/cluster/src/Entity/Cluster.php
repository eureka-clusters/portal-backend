<?php

declare(strict_types=1);

namespace Cluster\Entity;

use Application\Entity\AbstractEntity;
use Cluster\Repository\ClusterRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: 'cluster_cluster')]
#[ORM\Entity(repositoryClass: ClusterRepository::class)]
class Cluster extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private string $name = '';

    #[ORM\Column(unique: true)]
    private string $identifier = '';

    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $dateCreated;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?DateTime $dateUpdated = null;

    #[ORM\ManyToMany(targetEntity: Funder::class, mappedBy: 'clusters', cascade: ['persist'])]
    private Collection $clusterFunders;

    #[ORM\OneToMany(mappedBy: 'primaryCluster', targetEntity: Project::class, cascade: ['persist'])]
    private Collection $projectsPrimary;

    #[ORM\OneToMany(mappedBy: 'secondaryCluster', targetEntity: Project::class, cascade: ['persist'])]
    private Collection $projectsSecondary;

    public function __construct()
    {
        $this->dateCreated = new DateTime();
        $this->clusterFunders = new ArrayCollection();
        $this->projectsPrimary = new ArrayCollection();
        $this->projectsSecondary = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Cluster
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Cluster
    {
        $this->name = $name;
        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): Cluster
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Cluster
    {
        $this->description = $description;
        return $this;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): Cluster
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Cluster
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getClusterFunders(): ArrayCollection|Collection
    {
        return $this->clusterFunders;
    }

    public function setClusterFunders($funders): Cluster
    {
        $this->clusterFunders = $funders;
        return $this;
    }

    public function getProjectsPrimary(): ArrayCollection|Collection
    {
        return $this->projectsPrimary;
    }

    public function setProjectsPrimary($projectsPrimary): Cluster
    {
        $this->projectsPrimary = $projectsPrimary;
        return $this;
    }

    public function getProjectsSecondary(): ArrayCollection|Collection
    {
        return $this->projectsSecondary;
    }

    public function setProjectsSecondary($projectsSecondary): Cluster
    {
        $this->projectsSecondary = $projectsSecondary;
        return $this;
    }
}
