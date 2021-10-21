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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cluster_project_partner")
 * @ORM\Entity(repositoryClass="Cluster\Repository\Project\PartnerRepository")
 */
class Partner extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Organisation", inversedBy="partners", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private \Cluster\Entity\Organisation $organisation;

    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Project", inversedBy="partners", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private \Cluster\Entity\Project $project;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isActive;
    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isCoordinator;
    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isSelfFunded;
    /**
     * @ORM\Column(type="array")
     */
    private array $technicalContact = [];
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project\Version\CostsAndEffort", cascade={"persist"}, mappedBy="partner")
     * @var \Cluster\Entity\Project\Version\CostsAndEffort[]|ArrayCollection
     */
    private $costsAndEffort;

    public function __construct()
    {
        $this->costsAndEffort = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Partner
    {
        $this->id = $id;
        return $this;
    }

    public function getProject(): \Cluster\Entity\Project
    {
        return $this->project;
    }

    public function setProject(\Cluster\Entity\Project $project): Partner
    {
        $this->project = $project;
        return $this;
    }

    public function getOrganisation(): \Cluster\Entity\Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(\Cluster\Entity\Organisation $organisation): Partner
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): Partner
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function isCoordinator(): bool
    {
        return $this->isCoordinator;
    }

    public function setIsCoordinator(bool $isCoordinator): Partner
    {
        $this->isCoordinator = $isCoordinator;
        return $this;
    }

    public function isSelfFunded(): bool
    {
        return $this->isSelfFunded;
    }

    public function setIsSelfFunded(bool $isSelfFunded): Partner
    {
        $this->isSelfFunded = $isSelfFunded;
        return $this;
    }

    public function getTechnicalContact(): array
    {
        return $this->technicalContact;
    }

    public function setTechnicalContact(array $technicalContact): Partner
    {
        $this->technicalContact = $technicalContact;
        return $this;
    }

    public function getCostsAndEffort()
    {
        return $this->costsAndEffort;
    }

    public function setCostsAndEffort($costsAndEffort): Partner
    {
        $this->costsAndEffort = $costsAndEffort;
        return $this;
    }
}
