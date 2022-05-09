<?php

declare(strict_types=1);

namespace Cluster\Entity;

use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="cluster_country")
 * @ORM\Entity
 */
class Country extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /** @ORM\Column(length=2, unique=true) */
    private string $cd;
    /** @ORM\Column(unique=true) */
    private string $country;
    /**
     * @ORM\Column(name="docRef",type="string",unique=true)
     *
     * @Gedmo\Slug(fields={"country"})§
     */
    private string $docRef = '';
    /** @ORM\Column(type="string",nullable=true) */
    private ?string $iso3 = null;
    /** @ORM\Column(name="numcode",type="integer",length=6) */
    private int $numcode = 0;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Funder", cascade={"persist"}, mappedBy="country")
     */
    private Collection $funder;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Organisation", cascade={"persist"}, mappedBy="country")
     */
    private Collection $organisations;

    #[Pure] public function __construct()
    {
        $this->funder        = new ArrayCollection();
        $this->organisations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Country
    {
        $this->id = $id;
        return $this;
    }

    public function getCd(): string
    {
        return $this->cd;
    }

    public function setCd(string $cd): Country
    {
        $this->cd = $cd;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): Country
    {
        $this->country = $country;
        return $this;
    }

    public function getDocRef(): string
    {
        return $this->docRef;
    }

    public function setDocRef(string $docRef): Country
    {
        $this->docRef = $docRef;
        return $this;
    }

    public function getIso3(): ?string
    {
        return $this->iso3;
    }

    public function setIso3(?string $iso3): Country
    {
        $this->iso3 = $iso3;
        return $this;
    }

    public function getNumcode(): int
    {
        return $this->numcode;
    }

    public function setNumcode(int $numcode): Country
    {
        $this->numcode = $numcode;
        return $this;
    }

    public function getFunder(): ArrayCollection|Collection|array
    {
        return $this->funder;
    }

    public function setFunder($funder): Country
    {
        $this->funder = $funder;
        return $this;
    }

    public function getOrganisations(): ArrayCollection|Collection
    {
        return $this->organisations;
    }

    public function setOrganisations($organisations): Country
    {
        $this->organisations = $organisations;
        return $this;
    }
}
