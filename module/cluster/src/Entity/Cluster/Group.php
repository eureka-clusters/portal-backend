<?php

declare(strict_types=1);

namespace Cluster\Entity\Cluster;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Cluster;
use Cluster\Repository\Cluster\GroupRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Form\Annotation;
use Laminas\Form\Element\Hidden;

#[ORM\Table(name: 'cluster_cluster_group')]
#[ORM\Entity(repositoryClass: GroupRepository::class)]
class Group extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[Annotation\Type(Hidden::class)]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-cluster-cluster-group-name-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-cluster-cluster-group-name-label',
        'placeholder' => 'txt-cluster-cluster-group-name-placeholder',
    ])]
    private string $name = '';

    #[ORM\Column(nullable: true)]
    #[Annotation\Options(options: [
        'help-block' => 'txt-cluster-cluster-group-description-help-block',
    ])]
    #[Annotation\Attributes(attributes: [
        'label'       => 'txt-cluster-cluster-group-description-label',
        'placeholder' => 'txt-cluster-cluster-group-description-placeholder',
    ])]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    #[Gedmo\Timestampable(on: 'create')]
    #[Annotation\Exclude]
    private DateTime $dateCreated;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    #[Annotation\Exclude]
    private ?DateTime $dateUpdated = null;

    #[ORM\ManyToMany(targetEntity: Cluster::class, mappedBy: 'groups', cascade: ['persist'])]
    #[ORM\OrderBy(value: ['name' => Criteria::ASC])]
    #[ORM\JoinTable(name: 'cluster_cluster_group_cluster')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\InverseJoinColumn(nullable: false)]
    #[Annotation\Type(EntityMultiCheckbox::class)]
    #[Annotation\Options([
        'help-block'   => 'txt-cluster-group-clusters-help-block',
        'target_class' => Cluster::class,
        'find_method'  => [
            'name'   => 'findBy',
            'params' => ['criteria' => [], 'orderBy' => ['name' => Criteria::ASC]],
        ],
    ])]
    #[Annotation\Attributes(['label' => 'txt-cluster-group-clusters-label'])]
    private Collection $clusters;

    public function __construct()
    {
        $this->dateCreated = new DateTime();
        $this->clusters    = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Group
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Group
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Group
    {
        $this->description = $description;
        return $this;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): Group
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Group
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getClusters(): Collection
    {
        return $this->clusters;
    }

    public function setClusters(Collection $clusters): Group
    {
        $this->clusters = $clusters;
        return $this;
    }
}
