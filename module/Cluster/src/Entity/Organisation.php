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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cluster_organisation")
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
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Country", inversedBy="organisations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private \Cluster\Entity\Country $country;

    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Organisation\Type", inversedBy="organisations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private \Cluster\Entity\Organisation\Type $type;

    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project\Partner", cascade={"persist"}, mappedBy="organisation")
     * @var \Cluster\Entity\Project\Partner[]|ArrayCollection
     */
    private $partners;

    public function __construct()
    {
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

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): Organisation
    {
        $this->country = $country;
        return $this;
    }

    public function getType(): Organisation\Type
    {
        return $this->type;
    }

    public function setType(Organisation\Type $type): Organisation
    {
        $this->type = $type;
        return $this;
    }

    public function getPartners()
    {
        return $this->partners;
    }

    public function setPartners($partners)
    {
        $this->partners = $partners;
        return $this;
    }

}
