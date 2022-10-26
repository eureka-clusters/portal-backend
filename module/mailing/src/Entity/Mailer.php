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
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Text;

#[ORM\Table(name: 'mailing_mailer')]
#[ORM\Entity(repositoryClass: \Mailing\Repository\Mailer::class)]
#[Name('mailing_mailer')]
class Mailer extends AbstractEntity
{
    final public const MAILER_SERVICE_SMTP = 1;
    final public const MAILER_SERVICE_SENDMAIL = 2;
    final public const MAILER_SERVICE_MAILJET = 3;
    final public const MAILER_SERVICE_SENDGRID = 4;

    public static array $servicesArray = [
        self::MAILER_SERVICE_SMTP => 'txt-mailer-service-smtp',
        self::MAILER_SERVICE_SENDMAIL => 'txt-mailer-service-sendmail',
        self::MAILER_SERVICE_MAILJET => 'txt-mailer-service-mailjet',
        self::MAILER_SERVICE_SENDGRID => 'txt-mailer-service-sendgrid',
    ];

    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[Type(Hidden::class)]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[Type(Text::class)]
    #[Options(['help-block' => 'txt-mailer-service-help-block'])]
    #[Attributes(['label' => 'txt-mailer-service-label', 'placeholder' => 'txt-mailer-service-placeholder'])]
    private string $name = '';

    #[ORM\Column(type: 'smallint')]
    #[Exclude]
    private int $service = self::MAILER_SERVICE_SMTP;

    #[ORM\Column(nullable: true)]
    #[Type(Text::class)]
    #[Options(['help-block' => 'txt-mailer-hostname-help-block'])]
    #[Attributes(['label' => 'txt-mailer-hostname-label', 'placeholder' => 'txt-mailer-hostname-placeholder'])]
    private ?string $hostname = null;

    #[ORM\Column(nullable: true)]
    #[Type(Text::class)]
    #[Options(['help-block' => 'txt-mailer-username-help-block'])]
    #[Attributes(['label' => 'txt-mailer-username-label', 'placeholder' => 'txt-mailer-username-placeholder'])]
    private ?string $username = null;

    #[ORM\Column(nullable: true)]
    #[Type(Text::class)]
    #[Options(['help-block' => 'txt-mailer-password-help-block'])]
    #[Attributes(['label' => 'txt-mailer-password-label', 'placeholder' => 'txt-mailer-password-placeholder'])]
    private ?string $password = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    #[Type(Number::class)]
    #[Options(['help-block' => 'txt-mailer-port-help-block'])]
    #[Attributes(['label' => 'txt-mailer-port-label', 'placeholder' => 'txt-mailer-port-placeholder'])]
    private ?int $port = null;

    #[ORM\Column(name: '`ssl`', nullable: true)]
    #[Type(Text::class)]
    #[Options(['help-block' => 'txt-mailer-ssl-help-block'])]
    #[Attributes(['label' => 'txt-mailer-ssl-label', 'placeholder' => 'txt-mailer-ssl-placeholder'])]
    private ?string $ssl = null;

    #[ORM\Column(nullable: true)]
    #[Type(Text::class)]
    #[Options(['help-block' => 'txt-mailer-sendgrid-api-key-help-block'])]
    #[Attributes([
        'label' => 'txt-mailer-sendgrid-api-key-label',
        'placeholder' => 'txt-mailer-sendgrid-api-key-placeholder'
    ])]
    private ?string $sendGridApiKey = null;
    

    #[ORM\Column(type: 'boolean')]
    #[Type(Checkbox::class)]
    #[Options(['help-block' => 'txt-mailer-is-development-help-block'])]
    #[Attributes(['label' => 'txt-mailer-is-development-label'])]
    private bool $isDevelopment = false;

    #[ORM\Column(type: 'boolean')]
    #[Type(Checkbox::class)]
    #[Options(['help-block' => 'txt-mailer-is-active-help-block'])]
    #[Attributes(['label' => 'txt-mailer-is-active-label'])]
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'mailer', targetEntity: Transactional::class, cascade: ['persist'])]
    #[Exclude]
    private Collection $transactional;

    #[ORM\OneToMany(mappedBy: 'mailer', targetEntity: EmailMessage::class, cascade: ['persist'], fetch: 'EXTRA_LAZY')]
    #[Exclude]
    private Collection $emailMessage;

    #[Pure] public function __construct()
    {
        $this->transactional = new ArrayCollection();
        $this->emailMessage = new ArrayCollection();
    }

    public static function getServicesArray(): array
    {
        return self::$servicesArray;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function isMailjet(): bool
    {
        return $this->service === self::MAILER_SERVICE_MAILJET;
    }

    public function isSendGrid(): bool
    {
        return $this->service === self::MAILER_SERVICE_SENDGRID;
    }

    public function isSmtp(): bool
    {
        return $this->service === self::MAILER_SERVICE_SMTP;
    }

    public function isSendmail(): bool
    {
        return $this->service === self::MAILER_SERVICE_SENDMAIL;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Mailer
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Mailer
    {
        $this->name = $name;
        return $this;
    }

    public function getService(): int
    {
        return $this->service;
    }

    public function setService(int $service): Mailer
    {
        $this->service = $service;
        return $this;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(?string $hostname): Mailer
    {
        $this->hostname = $hostname;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): Mailer
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): Mailer
    {
        $this->password = $password;
        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(?int $port): Mailer
    {
        $this->port = $port;
        return $this;
    }

    public function getSsl(): ?string
    {
        return $this->ssl;
    }

    public function setSsl(?string $ssl): Mailer
    {
        $this->ssl = $ssl;
        return $this;
    }

    public function getSendGridApiKey(): ?string
    {
        return $this->sendGridApiKey;
    }

    public function setSendGridApiKey(?string $sendGridApiKey): Mailer
    {
        $this->sendGridApiKey = $sendGridApiKey;
        return $this;
    }

    public function isDevelopment(): bool
    {
        return $this->isDevelopment;
    }

    public function setIsDevelopment(bool $isDevelopment): Mailer
    {
        $this->isDevelopment = $isDevelopment;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): Mailer
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getTransactional(): Collection
    {
        return $this->transactional;
    }

    public function setTransactional(Collection $transactional): Mailer
    {
        $this->transactional = $transactional;
        return $this;
    }

    public function getEmailMessage(): Collection
    {
        return $this->emailMessage;
    }

    public function setEmailMessage(Collection $emailMessage): Mailer
    {
        $this->emailMessage = $emailMessage;
        return $this;
    }


  

}
