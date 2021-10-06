<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Entity\Project\Version;

use Cluster\Entity\Country;
use Cluster\Entity\Project;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cluster_project_version_costs_and_effort")
 * @ORM\Entity
 */
class CostsAndEffort
{
    public const RESULT_PROJECT = 1;
    public const RESULT_PARTNER = 2;
    public const RESULT_CHART   = 3;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Project\Partner", inversedBy="costsAndEffort", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private \Cluster\Entity\Project\Partner $partner;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Project\Version", inversedBy="costsAndEffort", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private \Cluster\Entity\Project\Version $version;
    /**
     * @ORM\Column(type="integer")
     */
    private int $year;
    /**
     * @ORM\Column(type="float")
     */
    private float $effort;
    /**
     * @ORM\Column(type="float")
     */
    private float $costs;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): CostsAndEffort
    {
        $this->id = $id;
        return $this;
    }

    public function getPartner(): Project\Partner
    {
        return $this->partner;
    }

    public function setPartner(Project\Partner $partner): CostsAndEffort
    {
        $this->partner = $partner;
        return $this;
    }

    public function getVersion(): Project\Version
    {
        return $this->version;
    }

    public function setVersion(Project\Version $version): CostsAndEffort
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