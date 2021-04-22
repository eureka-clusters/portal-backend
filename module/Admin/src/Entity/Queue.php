<?php
/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

namespace Admin\Entity;

use Application\Entity\AbstractEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use SlmQueue\Worker\Event\ProcessJobEvent;

/**
 * @ORM\Table(
 *     name="queue_default",
 *     indexes={
 *          @ORM\Index(name="pop", columns={"status", "queue", "scheduled", "priority"}),
 *          @ORM\Index(name="prune", columns={"status", "queue", "finished"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Admin\Repository\Queue")
 */
class Queue extends AbstractEntity
{
    private static array $statusTemplates = [
        ProcessJobEvent::JOB_STATUS_UNKNOWN             => "txt-job-status-unknown",
        ProcessJobEvent::JOB_STATUS_SUCCESS             => "txt-job-status-success",
        ProcessJobEvent::JOB_STATUS_FAILURE             => "txt-job-status-failure",
        ProcessJobEvent::JOB_STATUS_FAILURE_RECOVERABLE => "txt-status-failure-recoverable"
    ];

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(name="queue", type="string", length=64, nullable=false)
     * @var string
     */
    private $queue;
    /**
     * @ORM\Column(name="data", type="text", nullable=false)
     * @var string
     */
    private $data;
    /**
     * @ORM\Column(name="status", type="smallint", length=1, nullable=false)
     * @var int
     */
    private $status;
    /**
     * @ORM\Column(name="created", type="datetime", nullable=false)
     * @var DateTime
     */
    private $created;
    /**
     * @ORM\Column(name="scheduled", type="datetime", nullable=false)
     * @var DateTime
     */
    private $scheduled;
    /**
     * @ORM\Column(name="executed", type="datetime", )
     * @var DateTime
     */
    private $executed;
    /**
     * @ORM\Column(name="finished", type="datetime", )
     * @var DateTime
     */
    private $finished;
    /**
     * @ORM\Column(name="priority", type="integer", nullable=false, options={"default" : 1024})
     * @var int
     */
    private $priority;
    /**
     * @ORM\Column(name="message", type="text", )
     * @var string
     */
    private $message;
    /**
     * @ORM\Column(name="trace", type="text", )
     * @var string
     */
    private $trace;

    public function __toString(): string
    {
        return sprintf('%s %s', ucfirst($this->queue), $this->id);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Queue
    {
        $this->id = $id;
        return $this;
    }

    public function getQueue(): ?string
    {
        return $this->queue;
    }

    public function setQueue(?string $queue): Queue
    {
        $this->queue = $queue;
        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): Queue
    {
        $this->data = $data;
        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): Queue
    {
        $this->status = $status;
        return $this;
    }

    public function getStatusText(): string
    {
        return self::$statusTemplates[$this->status] ?? '';
    }

    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    public function setCreated(?DateTime $created): Queue
    {
        $this->created = $created;
        return $this;
    }

    public function getScheduled(): ?DateTime
    {
        return $this->scheduled;
    }

    public function setScheduled(?DateTime $scheduled): Queue
    {
        $this->scheduled = $scheduled;
        return $this;
    }

    public function getExecuted(): ?DateTime
    {
        return $this->executed;
    }

    public function setExecuted(?DateTime $executed): Queue
    {
        $this->executed = $executed;
        return $this;
    }

    public function getFinished(): ?DateTime
    {
        return $this->finished;
    }

    public function setFinished(?DateTime $finished): Queue
    {
        $this->finished = $finished;
        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): Queue
    {
        $this->priority = $priority;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): Queue
    {
        $this->message = $message;
        return $this;
    }

    public function getTrace(): ?string
    {
        return $this->trace;
    }

    public function setTrace(?string $trace): Queue
    {
        $this->trace = $trace;
        return $this;
    }
}
