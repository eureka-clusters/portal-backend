<?php

declare(strict_types=1);

namespace Cluster\Entity\Project\Version;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Project\Partner;
use Cluster\Entity\Project\Version;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cluster_project_version_costs_and_effort")
 * @ORM\Entity(repositoryClass="Cluster\Repository\Project\Version\CostsAndEffort")
 */
class CostsAndEffort extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Project\Partner", inversedBy="costsAndEffort", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Partner $partner;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Project\Version", inversedBy="costsAndEffort", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Version $version;
    /** @ORM\Column(type="integer") */
    private int $year = 2000;
    /** @ORM\Column(type="float") */
    private float $effort = 0.0;
    /** @ORM\Column(type="float") */
    private float $costs = 0.0;

    public function __construct()
    {
        $this->partner = new Partner();
        $this->version = new Version();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): CostsAndEffort
    {
        $this->id = $id;
        return $this;
    }

    public function getPartner(): Partner
    {
        return $this->partner;
    }

    public function setPartner(Partner $partner): CostsAndEffort
    {
        $this->partner = $partner;
        return $this;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): CostsAndEffort
    {
        $this->version = $version;
        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): CostsAndEffort
    {
        $this->year = $year;
        return $this;
    }

    public function getEffort(): float
    {
        return $this->effort;
    }

    public function setEffort(float $effort): CostsAndEffort
    {
        $this->effort = $effort;
        return $this;
    }

    public function getCosts(): float
    {
        return $this->costs;
    }

    public function setCosts(float $costs): CostsAndEffort
    {
        $this->costs = $costs;
        return $this;
    }
}
