<?php

declare(strict_types=1);

namespace Cluster\Entity;

use JetBrains\PhpStorm\Pure;
use Stringable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Entity\AbstractEntity;
use Cluster\Entity\Project;
use DateTime;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use function preg_replace;
use function strtolower;

/**
 * @ORM\Table(name="cluster_cluster")
 * @ORM\Entity(repositoryClass="Cluster\Repository\ClusterRepository")
 */
class Cluster extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /** @ORM\Column(unique=true) */
    private string $name;

    /** @ORM\Column(unique=true) */
    private string $identifier;

    /** @ORM\Column(nullable=true) */
    private ?string $description = null;
    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @Gedmo\Timestampable(on="create")
     */
    private DateTime $dateCreated;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Gedmo\Timestampable(on="update")
     */
    private ?DateTime $dateUpdated = null;
    /**
     * @ORM\ManyToMany(targetEntity="Cluster\Entity\Funder", cascade={"persist"}, mappedBy="clusters")
     */
    private Collection $clusterFunders;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project", cascade={"persist"}, mappedBy="primaryCluster")
     */
    private Collection $projectsPrimary;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project", cascade={"persist"}, mappedBy="secondaryCluster")
     */
    private Collection $projectsSecondary;

    #[Pure] public function __construct()
    {
        $this->clusterFunders    = new ArrayCollection();
        $this->projectsPrimary   = new ArrayCollection();
        $this->projectsSecondary = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->name;
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
