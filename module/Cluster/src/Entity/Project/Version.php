<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Entity\Project;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Project;
use DateTime;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cluster_project_version")
 * @ORM\Entity
 */
class Version extends AbstractEntity
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
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Project", inversedBy="versions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Project $project;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Version\Type", inversedBy="versions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private \Cluster\Entity\Version\Type $type;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project\Version\CostsAndEffort", cascade={"persist", "remove"}, mappedBy="version")
     *
     * @var \Cluster\Entity\Project\Version\CostsAndEffort[]|Collections\ArrayCollection
     */
    private $costsAndEffort;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private DateTime $submissionDate;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Version\Status", inversedBy="versions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private \Cluster\Entity\Version\Status $status;
    /**
     * @ORM\Column(type="float")
     */
    private float $effort;
    /**
     * @ORM\Column(type="float")
     */
    private float $costs;
    /**
     * @ORM\Column(type="array")
     */
    private array $countries = [];

    public function __construct()
    {
        $this->costsAndEffort = new Collections\ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Version
    {
        $this->id = $id;
        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): Version
    {
        $this->project = $project;
        return $this;
    }

    public function getType(): \Cluster\Entity\Version\Type
    {
        return $this->type;
    }

    public function setType(\Cluster\Entity\Version\Type $type): Version
    {
        $this->type = $type;
        return $this;
    }

    public function getSubmissionDate(): DateTime
    {
        return $this->submissionDate;
    }

    public function setSubmissionDate(DateTime $submissionDate): Version
    {
        $this->submissionDate = $submissionDate;
        return $this;
    }

    public function getStatus(): \Cluster\Entity\Version\Status
    {
        return $this->status;
    }

    public function setStatus(\Cluster\Entity\Version\Status $status): Version
    {
        $this->status = $status;
        return $this;
    }

    public function getEffort(): float
    {
        return $this->effort;
    }

    public function setEffort(float $effort): Version
    {
        $this->effort = $effort;
        return $this;
    }

    public function getCosts(): float
    {
        return $this->costs;
    }

    public function setCosts(float $costs): Version
    {
        $this->costs = $costs;
        return $this;
    }

    public function getCountries(): array
    {
        return $this->countries;
    }

    public function setCountries(array $countries): Version
    {
        $this->countries = $countries;
        return $this;
    }

    public function getCostsAndEffort()
    {
        return $this->costsAndEffort;
    }

    public function setCostsAndEffort($costsAndEffort): Version
    {
        $this->costsAndEffort = $costsAndEffort;
        return $this;
    }
}
