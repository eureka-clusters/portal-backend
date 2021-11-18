<?php

declare(strict_types=1);

namespace Cluster\Entity\Project;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Organisation;
use Cluster\Entity\Project;
use Cluster\Entity\Project\Version\CostsAndEffort;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
    private Organisation $organisation;

    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Project", inversedBy="partners", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Project $project;

    /**
     * @ORM\Column(unique=true)
     *
     * @Gedmo\Slug(fields={"projectName","organisationName"}, updatable=true)
     */
    private string $slug;
    /** @ORM\Column() */
    private string $organisationName;
    /** @ORM\Column() */
    private string $projectName;

    /** @ORM\Column(type="boolean") */
    private bool $isActive;
    /** @ORM\Column(type="boolean") */
    private bool $isCoordinator;
    /** @ORM\Column(type="boolean") */
    private bool $isSelfFunded;
    /** @ORM\Column(type="array") */
    private array $technicalContact = [];
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project\Version\CostsAndEffort", cascade={"persist"}, mappedBy="partner")
     *
     * @var CostsAndEffort[]|ArrayCollection
     */
    private $costsAndEffort;

    public function __construct()
    {
        $this->costsAndEffort = new ArrayCollection();
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): Partner
    {
        $this->project = $project;
        return $this;
    }

    public function getOrganisation(): Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(Organisation $organisation): Partner
    {
        $this->organisation = $organisation;
        return $this;
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Partner
    {
        $this->slug = $slug;
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

    public function setOrganisationName(string $organisationName): Partner
    {
        $this->organisationName = $organisationName;
        return $this;
    }

    public function setProjectName(string $projectName): Partner
    {
        $this->projectName = $projectName;
        return $this;
    }
}
