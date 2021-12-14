<?php

declare(strict_types=1);

namespace Cluster\Entity;

use Admin\Entity\User;
use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table(name="cluster_funder")
 * @ORM\Entity(repositoryClass="Cluster\Repository\FunderRepository")
 */
class Funder extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
    /**
     * @ORM\OneToOne(targetEntity="Admin\Entity\User",  cascade={"persist"}, inversedBy="funder")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Country", inversedBy="funder", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Country $country;
    /**
     * @ORM\ManyToMany(targetEntity="Cluster\Entity\Cluster", inversedBy="clusterFunders", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"description"="ASC"})
     * @ORM\JoinTable(name="cluster_funder_cluster",
     *      joinColumns={@ORM\JoinColumn(nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(nullable=false)}
     * )
     */
    private Collection $clusters;

    #[Pure] public function __construct()
    {
        $this->clusters = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Funder
    {
        $this->id = $id;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Funder
    {
        $this->user = $user;
        return $this;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): Funder
    {
        $this->country = $country;
        return $this;
    }

    public function getClusters(): ArrayCollection|Collection
    {
        return $this->clusters;
    }

    public function setClusters(ArrayCollection|Collection $clusters): Funder
    {
        $this->clusters = $clusters;
        return $this;
    }
}
