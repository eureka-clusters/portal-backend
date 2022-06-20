<?php

declare(strict_types=1);

namespace Cluster\Entity\Project;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Project;
use Cluster\Entity\Version\Status;
use Cluster\Entity\Version\Type;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cluster_project_version")
 * @ORM\Entity
 */
class Version extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Project", inversedBy="versions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Project $project;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Version\Type", inversedBy="versions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Type $type;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project\Version\CostsAndEffort", cascade={"persist", "remove"}, mappedBy="version")
     */
    private Collection $costsAndEffort;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $submissionDate = null;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Version\Status", inversedBy="versions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Status $status;
    /** @ORM\Column(type="float") */
    private float $effort = 0.0;
    /** @ORM\Column(type="float") */
    private float $costs = 0.0;
    /** @ORM\Column(type="array") */
    private array $countries = [];
    /**
     * @ORM\OneToOne(targetEntity="Cluster\Entity\Project\Evaluation", cascade={"persist"}, mappedBy="projectVersion")
     */
    private ?\Cluster\Entity\Project\Evaluation $evaluation = null;

    public function __construct()
    {
        $this->project = new Project();
        $this->type = new Type();
        $this->status = new Status();
        $this->costsAndEffort = new ArrayCollection();
    }

    public function hasEvaluation(): bool
    {
        return null !== $this->evaluation;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Version
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

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): Version
    {
        $this->type = $type;
        return $this;
    }

    public function getCostsAndEffort(): ArrayCollection|Collection
    {
        return $this->costsAndEffort;
    }

    public function setCostsAndEffort(ArrayCollection|Collection $costsAndEffort): Version
    {
        $this->costsAndEffort = $costsAndEffort;
        return $this;
    }

    public function getSubmissionDate(): ?DateTime
    {
        return $this->submissionDate;
    }

    public function setSubmissionDate(?DateTime $submissionDate): Version
    {
        $this->submissionDate = $submissionDate;
        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): Version
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

    public function getEvaluation(): ?Evaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(?Evaluation $evaluation): Version
    {
        $this->evaluation = $evaluation;
        return $this;
    }
}
