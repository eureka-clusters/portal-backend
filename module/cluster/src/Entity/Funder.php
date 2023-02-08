<?php

declare(strict_types=1);

namespace Cluster\Entity;

use Admin\Entity\User;
use Application\Entity\AbstractEntity;
use Cluster\Repository\FunderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'cluster_funder')]
#[ORM\Entity(repositoryClass: FunderRepository::class)]
class Funder extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\OneToOne(inversedBy: 'funder', targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Country::class, cascade: ['persist'], inversedBy: 'funder')]
    #[ORM\JoinColumn(nullable: false)]
    private Country $country;

    #[ORM\ManyToMany(targetEntity: Cluster::class, inversedBy: 'clusterFunders', cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\OrderBy(value: ['description' => Criteria::ASC])]
    #[ORM\JoinTable(name: 'cluster_funder_cluster')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\InverseJoinColumn(nullable: false)]
    private Collection $clusters;

    public function __construct()
    {
        $this->user     = new User();
        $this->country  = new Country();
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

    public function getClusters(): Collection
    {
        return $this->clusters;
    }

    public function setClusters(Collection $clusters): Funder
    {
        $this->clusters = $clusters;
        return $this;
    }
}
