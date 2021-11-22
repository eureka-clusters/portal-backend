<?php

declare(strict_types=1);

namespace Admin\Entity\Api;

use Application\Entity\AbstractEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="admin_api_log")
 * @ORM\Entity(repositoryClass="Admin\Repository\Api\Log")
 */
class Log extends AbstractEntity
{
    public const TYPE_INCOMING = 1;
    public const TYPE_OUTGOING = 2;

    protected static array $typeTemplates
        = [
            self::TYPE_INCOMING => 'txt-type-incoming',
            self::TYPE_OUTGOING => 'txt-type-outgoing',
        ];

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
    /** @ORM\Column() */
    private string $class;
    /** @ORM\Column(type="smallint") */
    private int $type;
    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     */
    private DateTime $dateCreated;
    /** @ORM\Column(type="text") */
    private string $payload;
    /** @ORM\Column(type="integer") */
    private int $statusCode;
    /** @ORM\Column(type="text") */
    private string $status;
    /** @ORM\Column(type="text") */
    private ?string $response;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Log
    {
        $this->id = $id;
        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): Log
    {
        $this->class = $class;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): Log
    {
        $this->type = $type;
        return $this;
    }

    public function getTypeText(): string
    {
        return self::$typeTemplates[$this->type];
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): Log
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function setPayload(string $payload): Log
    {
        $this->payload = $payload;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): Log
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Log
    {
        $this->status = $status;
        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): Log
    {
        $this->response = $response;
        return $this;
    }
}
