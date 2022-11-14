<?php

declare(strict_types=1);

namespace Cluster\Entity\Project\Partner;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Funding\Status;
use Cluster\Entity\Project\Partner;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use function date;

#[ORM\Table(name: 'cluster_project_partner_funding')]
#[ORM\UniqueConstraint(name: 'cluster_project_partner_funding_year', columns: ['partner_id', 'year'])]
#[ORM\Entity(repositoryClass: \Cluster\Repository\Partner\Funding::class)]
class Funding extends AbstractEntity
{
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'smallint', nullable: false)]
    private int $year;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?DateTime $dateUpdated = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?DateTime $dateCreated = null;

    #[ORM\ManyToOne(targetEntity: Status::class, cascade: ['persist'], inversedBy: 'funding')]
    #[ORM\JoinColumn(nullable: false)]
    private Status $status;

    #[ORM\ManyToOne(targetEntity: Partner::class, cascade: ['persist'], inversedBy: 'funding')]
    #[ORM\JoinColumn(nullable: false)]
    private Partner $partner;

    public function __construct()
    {
        $this->year    = (int) date(format: 'Y');
        $this->status  = new Status();
        $this->partner = new Partner();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Funding
    {
        $this->id = $id;
        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): Funding
    {
        $this->year = $year;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Funding
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getDateCreated(): ?DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTime $dateCreated): Funding
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): Funding
    {
        $this->status = $status;
        return $this;
    }

    public function getPartner(): Partner
    {
        return $this->partner;
    }

    public function setPartner(Partner $partner): Funding
    {
        $this->partner = $partner;
        return $this;
    }
}
