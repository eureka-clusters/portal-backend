<?php

declare(strict_types=1);

namespace Deeplink\Entity;

use Admin\Entity\User;
use Application\Entity\AbstractEntity;
use DateInterval;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Math\Rand;

use function sha1;
use function substr;

#[ORM\Table(name: 'deeplink')]
#[ORM\Entity(repositoryClass: \Deeplink\Repository\Deeplink::class)]
class Deeplink extends AbstractEntity
{
    final public const EXPIRATION_DAYS_DEFAULT = 100;

    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $hash;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $dateCreated;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private DateTime $endDate;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $dateAccess = null;

    #[ORM\Column(type: 'string', length: 9, nullable: true)]
    private null|string|int $keyId = null;

    #[ORM\ManyToOne(targetEntity: Target::class, cascade: ['persist'], inversedBy: 'deeplink')]
    #[ORM\JoinColumn(nullable: false)]
    private Target $target;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'deeplink')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function __construct()
    {
        $this->dateCreated = new DateTime();
        $this->endDate = (new DateTime())->add(
            interval: new DateInterval(
            duration: sprintf(
            'P%dD',
            self::EXPIRATION_DAYS_DEFAULT
        )
        )
        );
        $this->user = new User();
        $this->target = new Target();
        $this->hash = substr(string: sha1(string: Rand::getString(length: 255)), offset: 0, length: 15);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Deeplink
    {
        $this->id = $id;
        return $this;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): Deeplink
    {
        $this->hash = $hash;
        return $this;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): Deeplink
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate): Deeplink
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getDateAccess(): ?DateTime
    {
        return $this->dateAccess;
    }

    public function setDateAccess(?DateTime $dateAccess): Deeplink
    {
        $this->dateAccess = $dateAccess;
        return $this;
    }

    public function getKeyId(): null|string|int
    {
        return $this->keyId;
    }

    public function setKeyId(null|string|int $keyId): Deeplink
    {
        $this->keyId = $keyId;
        return $this;
    }

    public function getTarget(): Target
    {
        return $this->target;
    }

    public function setTarget(Target $target): Deeplink
    {
        $this->target = $target;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Deeplink
    {
        $this->user = $user;
        return $this;
    }
}
