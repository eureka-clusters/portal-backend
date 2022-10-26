<?php

declare(strict_types=1);

namespace Mailing\Entity;

use Application\Entity\AbstractEntity;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Math\Rand;

use function sha1;

#[ORM\Table(name: 'mailing_email_message')]
#[ORM\Entity(repositoryClass: \Mailing\Repository\EmailMessage::class)]
class EmailMessage extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(unique: true)]
    private string $identifier;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $dateCreated;

    #[ORM\ManyToOne(targetEntity: \Admin\Entity\User::class, cascade: ['persist'], inversedBy: 'emailMessage')]
    #[ORM\JoinColumn(nullable: true)]
    private ?\Admin\Entity\User $user = null;

    #[ORM\ManyToOne(targetEntity: Template::class, cascade: ['persist'], inversedBy: 'emailMessage')]
    #[ORM\JoinColumn(nullable: false)]
    private Template $template;

    #[ORM\ManyToOne(targetEntity: Mailer::class, cascade: ['persist'], inversedBy: 'emailMessage')]
    #[ORM\JoinColumn(nullable: false)]
    private Mailer $mailer;

    #[ORM\ManyToOne(targetEntity: Sender::class, cascade: ['persist'], inversedBy: 'emailMessage')]
    #[ORM\JoinColumn(nullable: false)]
    private Sender $sender;

    #[ORM\Column]
    private string $emailAddress = '';

    #[ORM\Column]
    private string $subject = '';

    #[ORM\Column(name: 'toUser', type: 'array')]
    private array $to = [];

    #[ORM\Column(type: 'array', nullable: true)]
    private ?array $cc = null;

    #[ORM\Column(type: 'array', nullable: true)]
    private ?array $bcc = null;

    #[ORM\Column(type: 'text')]
    private string $message = '';

    #[ORM\Column(type: 'smallint')]
    private int $amountOfAttachments = 0;

    #[ORM\OneToMany(mappedBy: 'emailMessage', targetEntity: EmailMessageEvent::class, cascade: ['persist', 'remove'])]
    private Collection $event;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $latestEvent = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $dateLatestEvent = null;

    public function __construct()
    {
        $this->dateCreated = new DateTime();
        $this->sender = new Sender();
        $this->template = new Template();
        $this->mailer = new Mailer();
        $this->identifier = sha1(string: Rand::getString(length: 30));
        $this->event = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->subject;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): EmailMessage
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): EmailMessage
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): EmailMessage
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getUser(): ?\Admin\Entity\User
    {
        return $this->user;
    }

    public function setUser(?\Admin\Entity\User $user): EmailMessage
    {
        $this->user = $user;
        return $this;
    }

    public function getTemplate(): Template
    {
        return $this->template;
    }

    public function setTemplate(Template $template): EmailMessage
    {
        $this->template = $template;
        return $this;
    }

    public function getMailer(): Mailer
    {
        return $this->mailer;
    }

    public function setMailer(Mailer $mailer): EmailMessage
    {
        $this->mailer = $mailer;
        return $this;
    }

    public function getSender(): Sender
    {
        return $this->sender;
    }

    public function setSender(Sender $sender): EmailMessage
    {
        $this->sender = $sender;
        return $this;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): EmailMessage
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): EmailMessage
    {
        $this->subject = $subject;
        return $this;
    }

    public function getTo(): array
    {
        return $this->to;
    }

    public function setTo(array $to): EmailMessage
    {
        $this->to = $to;
        return $this;
    }

    public function getCc(): ?array
    {
        return $this->cc;
    }

    public function setCc(?array $cc): EmailMessage
    {
        $this->cc = $cc;
        return $this;
    }

    public function getBcc(): ?array
    {
        return $this->bcc;
    }

    public function setBcc(?array $bcc): EmailMessage
    {
        $this->bcc = $bcc;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): EmailMessage
    {
        $this->message = $message;
        return $this;
    }

    public function getAmountOfAttachments(): int
    {
        return $this->amountOfAttachments;
    }

    public function setAmountOfAttachments(int $amountOfAttachments): EmailMessage
    {
        $this->amountOfAttachments = $amountOfAttachments;
        return $this;
    }

    public function getEvent(): Collection
    {
        return $this->event;
    }

    public function setEvent(Collection $event): EmailMessage
    {
        $this->event = $event;
        return $this;
    }

    public function getLatestEvent(): ?string
    {
        return $this->latestEvent;
    }

    public function setLatestEvent(?string $latestEvent): EmailMessage
    {
        $this->latestEvent = $latestEvent;
        return $this;
    }

    public function getDateLatestEvent(): ?DateTime
    {
        return $this->dateLatestEvent;
    }

    public function setDateLatestEvent(?DateTime $dateLatestEvent): EmailMessage
    {
        $this->dateLatestEvent = $dateLatestEvent;
        return $this;
    }


}
