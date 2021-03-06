<?php

declare(strict_types=1);

namespace Cluster\Entity;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Organisation\Type;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="cluster_organisation",
 *     indexes={
 *      @ORM\Index(flags={"fulltext"}, columns={"name"})
 * })
 * @ORM\Entity(repositoryClass="Cluster\Repository\OrganisationRepository")
 */
class Organisation extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
    /**
     * @ORM\Column()
     */
    private string $name;
    /**
     * @ORM\Column(unique=true)
     *
     * @Gedmo\Slug(fields={"name"})
     */
    private string $slug;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Country", inversedBy="organisations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Country $country;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Organisation\Type", inversedBy="organisations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Type $type;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project\Partner", cascade={"persist"}, mappedBy="organisation")
     */
    private Collection $partners;

    #[Pure] public function __construct()
    {
        $this->country  = new Country();
        $this->type     = new Type();
        $this->partners = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Organisation
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Organisation
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Organisation
    {
        $this->slug = $slug;
        return $this;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): Organisation
    {
        $this->country = $country;
        return $this;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): Organisation
    {
        $this->type = $type;
        return $this;
    }

    public function getPartners(): ArrayCollection|Collection
    {
        return $this->partners;
    }

    public function setPartners(ArrayCollection|Collection $partners): Organisation
    {
        $this->partners = $partners;
        return $this;
    }
}
