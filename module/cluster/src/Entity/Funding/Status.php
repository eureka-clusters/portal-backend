<?php

declare(strict_types=1);

namespace Cluster\Entity\Funding;

use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Laminas\Form\Annotation;

/**
 * This table contains the detailed funding status per partner (and is not used yet)
 *
 * @ORM\Table(name="cluster_funding_status")
 * @ORM\Entity
 */
class Status extends AbstractEntity
{
    public const IS_EVALUATION = 1;
    public const IS_NOT_EVALUATION = 2;

    public const STATUS_ALL_GOOD = 1;
    public const STATUS_GOOD = 2;
    public const STATUS_BAD = 3;
    public const STATUS_FAILED = 4;
    public const STATUS_UNCLEAR = 5;
    public const STATUS_AVERAGE = 6;
    public const STATUS_SELF_FUNDED = 7;
    public const STATUS_DEFAULT = 8;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private string $code = '';
    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private string $status = '';
    /**
     * @ORM\Column(type="string", length=7, nullable=false, unique=true)
     */
    private string $color = '#FF0000';
    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private string $statusFunding = '';
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $isEvaluation = true;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $statusEvaluation = null;
    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    private int $sequence = 1;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project\Evaluation", cascade={"persist"}, mappedBy="status")
     */
    private Collection $evaluation;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project\Partner\Funding", cascade={"persist"}, mappedBy="status")
     */
    private Collection $funding;

    #[Pure] public function __construct()
    {
        $this->evaluation = new ArrayCollection();
        $this->funding = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->statusEvaluation;
    }

    public function parseStyle(): string
    {
        return 'background-color: ' . $this->color;
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): Status
    {
        $this->code = $code;
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

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): Status
    {
        $this->color = $color;
        return $this;
    }

    public function getStatusFunding(): string
    {
        return $this->statusFunding;
    }

    public function setStatusFunding(string $statusFunding): Status
    {
        $this->statusFunding = $statusFunding;
        return $this;
    }

    public function isEvaluation(): bool
    {
        return $this->isEvaluation;
    }

    public function setIsEvaluation(bool $isEvaluation): Status
    {
        $this->isEvaluation = $isEvaluation;
        return $this;
    }

    public function getStatusEvaluation(): ?string
    {
        return $this->statusEvaluation;
    }

    public function setStatusEvaluation(?string $statusEvaluation): Status
    {
        $this->statusEvaluation = $statusEvaluation;
        return $this;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function setSequence(int $sequence): Status
    {
        $this->sequence = $sequence;
        return $this;
    }

    public function getEvaluation(): Collection
    {
        return $this->evaluation;
    }

    public function setEvaluation(Collection $evaluation): Status
    {
        $this->evaluation = $evaluation;
        return $this;
    }

    public function getFunding(): Collection
    {
        return $this->funding;
    }

    public function setFunding(Collection $funding): Status
    {
        $this->funding = $funding;
        return $this;
    }


}
