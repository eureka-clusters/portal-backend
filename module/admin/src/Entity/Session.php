<?php

declare(strict_types=1);

namespace Admin\Entity;

use Stringable;
use Application\Entity\AbstractEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="admin_session",indexes={@ORM\Index(name="session_key_idx", columns={"key"})})
 * @ORM\Entity
 */
class Session extends AbstractEntity implements Stringable
{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;
    /** @ORM\Column(name="`key`") */
    private string $key;
    /** @ORM\Column(type="integer") */
    private int $modified;
    /** @ORM\Column(type="integer") */
    private int $lifetime;
    /** @ORM\Column(type="integer") */
    private int $hits;
    /** @ORM\Column(type="text") */
    private string $data;
    /** @ORM\Column() */
    private string $name;
    /** @ORM\Column(type="string", length=15, nullable=false) */
    private $ip;
    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @Gedmo\Timestampable(on="create")
     */
    private DateTime $dateStart;
    /**
     * @ORM\ManyToOne(targetEntity="Admin\Entity\User", inversedBy="session", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private User $user;

    public function __construct()
    {
        $this->ip   = $_SERVER['REMOTE_ADDR'] ?? '-';
        $this->hits = 1;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Session
    {
        $this->id = $id;
        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): Session
    {
        $this->key = $key;
        return $this;
    }

    public function getModified(): int
    {
        return $this->modified;
    }

    public function setModified(int $modified): Session
    {
        $this->modified = $modified;
        return $this;
    }

    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    public function setLifetime(int $lifetime): Session
    {
        $this->lifetime = $lifetime;
        return $this;
    }

    public function getHits(): int
    {
        return $this->hits;
    }

    public function setHits(int $hits): Session
    {
        $this->hits = $hits;
        return $this;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): Session
    {
        $this->data = $data;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Session
    {
        $this->name = $name;
        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): Session
    {
        $this->ip = $ip;
        return $this;
    }

    public function getDateStart(): ?DateTime
    {
        return $this->dateStart;
    }

    public function setDateStart(DateTime $dateStart): Session
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): Session
    {
        $this->user = $user;
        return $this;
    }
}
