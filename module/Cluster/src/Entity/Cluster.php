<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Entity;

use Application\Entity\AbstractEntity;
use DateTime;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
    /**
     * @ORM\Column(unique=true)
     */
    private string $name;

    /**
     * @ORM\Column(unique=true)
     */
    private string $identifier;

    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $description = null;
    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    private DateTime $dateCreated;
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private ?DateTime $dateUpdated;
    /**
     * @ORM\ManyToMany(targetEntity="Cluster\Entity\Funder", cascade={"persist"}, mappedBy="clusters")
     *
     * @var Funder[]|Collections\ArrayCollection
     */
    private $clusterFunders;

    public function __construct()
    {
        $this->clusterFunders = new Collections\ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->name;
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

    public function getClusterFunders()
    {
        return $this->clusterFunders;
    }

    public function setClusterFunders($funders)
    {
        $this->clusterFunders = $funders;
        return $this;
    }

    /**
     * @param Funder $funder
     */
    public function addFunder(Funder $funder)
    {
        if ($this->clusterFunders->contains($funder)) {
            return;
        }

        $this->clusterFunders->add($funder);
        $funder->addCluster($this);
    }

    /**
     * @param Funder $funder
     */
    public function removeFunder(Funder $funder)
    {
        if (!$this->clusterFunders->contains($funder)) {
            return;
        }

        $this->clusterFunders->removeElement($funder);
        $funder->removeCluster($this);
    }

}
