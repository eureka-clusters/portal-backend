<?php

declare(strict_types=1);

namespace Mailing\Entity;

use Application\Entity\AbstractEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'mailing_email_message_event')]
#[ORM\Entity]
class EmailMessageEvent extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: EmailMessage::class, cascade: ['persist'], inversedBy: 'event')]
    #[ORM\JoinColumn(nullable: false)]
    private EmailMessage $emailMessage;

    #[ORM\Column]
    private string $event;

    #[ORM\Column(type: 'datetime')]
    private DateTime $time;

    #[ORM\Column(type: 'bigint', nullable: false)]
    private ?int $messageId = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $campaign = null;

    #[ORM\Column(name: "smpt_reply", type: 'text', nullable: true)]
    private ?string $smtpReply = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $ip = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $agent = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $error = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $errorRelatedTo = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $source = null;

    public function __construct()
    {
        $this->emailMessage = new EmailMessage();
        $this->time = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(?int $id): EmailMessageEvent
    {
        $this->id = $id;
        return $this;
    }

    public function getEmailMessage(): EmailMessage
    {
        return $this->emailMessage;
    }

    public function setEmailMessage(EmailMessage $emailMessage): EmailMessageEvent
    {
        $this->emailMessage = $emailMessage;
        return $this;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function setEvent(string $event): EmailMessageEvent
    {
        $this->event = $event;
        return $this;
    }

    public function getTime(): ?DateTime
    {
        return $this->time;
    }

    public function setTime(?DateTime $time): EmailMessageEvent
    {
        $this->time = $time;
        return $this;
    }

    public function getMessageId(): ?int
    {
        return $this->messageId;
    }

    public function setMessageId(?int $messageId): EmailMessageEvent
    {
        $this->messageId = $messageId;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): EmailMessageEvent
    {
        $this->email = $email;
        return $this;
    }

    public function getCampaign(): ?string
    {
        return $this->campaign;
    }

    public function setCampaign(?string $campaign): EmailMessageEvent
    {
        $this->campaign = $campaign;
        return $this;
    }

    public function getSmtpReply(): ?string
    {
        return $this->smtpReply;
    }

    public function setSmtpReply(?string $smtpReply): EmailMessageEvent
    {
        $this->smtpReply = $smtpReply;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): EmailMessageEvent
    {
        $this->url = $url;
        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): EmailMessageEvent
    {
        $this->ip = $ip;
        return $this;
    }

    public function getAgent(): ?string
    {
        return $this->agent;
    }

    public function setAgent(?string $agent): EmailMessageEvent
    {
        $this->agent = $agent;
        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): EmailMessageEvent
    {
        $this->error = $error;
        return $this;
    }

    public function getErrorRelatedTo(): ?string
    {
        return $this->errorRelatedTo;
    }

    public function setErrorRelatedTo(?string $errorRelatedTo): EmailMessageEvent
    {
        $this->errorRelatedTo = $errorRelatedTo;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): EmailMessageEvent
    {
        $this->source = $source;
        return $this;
    }
}
