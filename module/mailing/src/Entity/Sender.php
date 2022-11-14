<?php

declare(strict_types=1);

namespace Mailing\Entity;

use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Laminas\Form\Annotation\Attributes;
use Laminas\Form\Annotation\Exclude;
use Laminas\Form\Annotation\Name;
use Laminas\Form\Annotation\Options;
use Laminas\Form\Annotation\Type;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;

use function sprintf;

#[ORM\Table(name: 'mailing_sender')]
#[ORM\Entity(repositoryClass: \Mailing\Repository\Sender::class)]
#[Name(name: 'mailing_sender')]
class Sender extends AbstractEntity
{
    final public const SENDER_DEFAULT        = 1;
    final public const SENDER_OWNER          = 2;
    final public const SENDER_LOGGED_IN_USER = 3;

    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[Type(type: Hidden::class)]
    private ?int $id = null;

    #[ORM\Column]
    #[Type(type: Text::class)]
    #[Options(options: ['help-block' => 'txt-sender-name-help-block'])]
    #[Attributes(attributes: ['label' => 'txt-sender-name-label', 'placeholder' => 'txt-sender-name-placeholder'])]
    private string $sender = '';

    #[ORM\Column]
    #[Type(type: Email::class)]
    #[Options(options: ['help-block' => 'txt-sender-email-help-block'])]
    #[Attributes(attributes: ['label' => 'txt-sender-email-label', 'placeholder' => 'txt-sender-email-placeholder'])]
    private string $email = '';

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Transactional::class, cascade: ['persist'])]
    #[Exclude]
    private Collection $transactional;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: EmailMessage::class, cascade: ['persist'])]
    #[Exclude]
    private Collection $emailMessage;

    #[Pure] public function __construct()
    {
        $this->transactional = new ArrayCollection();
        $this->emailMessage  = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->sender, $this->email);
    }

    public function isOwner(): bool
    {
        return $this->id === self::SENDER_OWNER;
    }

    public function isLoggedInUser(): bool
    {
        return $this->id === self::SENDER_LOGGED_IN_USER;
    }

    public function isDefault(): bool
    {
        return $this->id === self::SENDER_DEFAULT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Sender
    {
        $this->id = $id;
        return $this;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setSender(string $sender): Sender
    {
        $this->sender = $sender;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Sender
    {
        $this->email = $email;
        return $this;
    }

    public function getTransactional(): Collection
    {
        return $this->transactional;
    }

    public function setTransactional($transactional): Sender
    {
        $this->transactional = $transactional;
        return $this;
    }

    public function getEmailMessage(): Collection
    {
        return $this->emailMessage;
    }

    public function setEmailMessage(Collection $emailMessage): Sender
    {
        $this->emailMessage = $emailMessage;
        return $this;
    }
}
