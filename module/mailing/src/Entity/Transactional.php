<?php

declare(strict_types=1);

namespace Mailing\Entity;

use Application\Entity\AbstractEntity;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use DoctrineORMModule\Form\Element\EntitySelect;
use Gedmo\Mapping\Annotation as Gedmo;
use Laminas\Form\Annotation\Attributes;
use Laminas\Form\Annotation\Exclude;
use Laminas\Form\Annotation\Name;
use Laminas\Form\Annotation\Options;
use Laminas\Form\Annotation\Type;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;

use function array_key_exists;

#[ORM\Table(name: 'mailing_transactional')]
#[ORM\Entity(repositoryClass: \Mailing\Repository\Transactional::class)]
#[Name(name: 'mailing_transactional')]
class Transactional extends AbstractEntity
{
    final public const TRANSACTIONAL_MAILING_QUEUE_START = 'mailing:queue:start';
    final public const TRANSACTIONAL_MAILING_QUEUE_END   = 'mailing:queue:end';

    public static array $lockedKeys
        = [
            self::TRANSACTIONAL_MAILING_QUEUE_START,
            self::TRANSACTIONAL_MAILING_QUEUE_END,
        ];

    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[Type(type: Hidden::class)]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[Type(type: Text::class)]
    #[Options(options: ['label' => 'txt-transactional-name-label', 'help-block' => 'txt-transactional-name-help-block'])]
    #[Attributes(attributes: ['placeholder' => 'txt-transactional-name-placeholder'])]
    private string $name = '';

    #[ORM\Column(name: 'unique_key', unique: true)]
    #[Type(type: Text::class)]
    #[Options(options: ['label' => 'txt-transactional-key-label', 'help-block' => 'txt-transactional-key-help-block'])]
    #[Attributes(attributes: ['placeholder' => 'txt-transactional-key-placeholder'])]
    private string $key = '';

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    #[Exclude]
    private DateTime $dateCreated;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    #[Exclude]
    private ?DateTime $lastUpdate = null;
    #[ORM\Column(type: 'text')]
    #[Type(type: Textarea::class)]
    #[Options(options: [
        'label'      => 'txt-transactional-mail-html-label',
        'help-block' => 'txt-transactional-mail-html-help-block',
    ])]
    #[Attributes(attributes: ['rows' => 30, 'id' => 'mailHtml'])]
    private string $mailHtml      = '';

    #[ORM\Column]
    #[Type(type: Text::class)]
    #[Options(options: ['label' => 'txt-mailing-mail-subject-label', 'help-block' => 'txt-mailing-mail-subject-help-block'])]
    #[Attributes(attributes: ['placeholder' => 'txt-mailing-mail-subject-placeholder'])]
    private string $mailSubject = '';

    #[ORM\ManyToOne(targetEntity: Template::class, cascade: ['persist'], inversedBy: 'transactional')]
    #[ORM\JoinColumn(nullable: false)]
    #[Type(type: EntitySelect::class)]
    #[Options(options: [
        'help-block'   => 'txt-mailing-transactional-template-help-block',
        'empty_option' => '— Select a mailing template',
        'target_class' => Template::class,
        'find_method'  => ['name' => 'findBy', 'params' => ['criteria' => [], 'orderBy' => ['name' => Criteria::ASC]]],
    ])]
    #[Attributes(attributes: ['label' => 'txt-mailing-transactional-template-label'])]
    private Template $template;

    #[ORM\ManyToOne(targetEntity: Sender::class, cascade: ['persist'], inversedBy: 'transactional')]
    #[ORM\JoinColumn(nullable: false)]
    #[Type(type: EntitySelect::class)]
    #[Options(options: [
        'help-block'   => 'txt-mailing-transactional-sender-help-block',
        'empty_option' => '— Select a sender',
        'target_class' => Sender::class,
        'find_method'  => [
            'name'   => 'findBy',
            'params' => ['criteria' => [], 'orderBy' => ['sender' => Criteria::ASC]],
        ],
    ])]
    #[Attributes(attributes: ['label' => 'txt-mailing-transactional-sender-label'])]
    private Sender $sender;

    #[ORM\ManyToOne(targetEntity: Mailer::class, cascade: ['persist'], inversedBy: 'transactional')]
    #[ORM\JoinColumn(nullable: false)]
    #[Type(type: EntitySelect::class)]
    #[Options(options: [
        'help-block'   => 'txt-mailing-transactional-mailer-help-block',
        'empty_option' => '— Select a mailer',
        'target_class' => Mailer::class,
        'find_method'  => [
            'name'   => 'findBy',
            'params' => [
                'criteria' => ['isActive' => true],
                'orderBy'  => ['name' => Criteria::ASC],
            ],
        ],
    ])]
    #[Attributes(attributes: ['label' => 'txt-mailing-transactional-mailer-label'])]
    private Mailer $mailer;

    public function __construct()
    {
        $this->dateCreated = new DateTime();
        $this->sender      = new Sender();
        $this->mailer      = new Mailer();
        $this->template    = new Template();
    }

    public function parseSourceContent(): string
    {
        return $this->mailHtml;
    }

    public function parseName(): string
    {
        return $this->key;
    }

    public function isLocked(): bool
    {
        return array_key_exists(key: $this->key, array: self::$lockedKeys);
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Transactional
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Transactional
    {
        $this->name = $name;
        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): Transactional
    {
        $this->key = $key;
        return $this;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): Transactional
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getLastUpdate(): ?DateTime
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?DateTime $lastUpdate): Transactional
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    public function getMailHtml(): string
    {
        return $this->mailHtml;
    }

    public function setMailHtml(string $mailHtml): Transactional
    {
        $this->mailHtml = $mailHtml;
        return $this;
    }

    public function getMailSubject(): string
    {
        return $this->mailSubject;
    }

    public function setMailSubject(string $mailSubject): Transactional
    {
        $this->mailSubject = $mailSubject;
        return $this;
    }

    public function getTemplate(): Template
    {
        return $this->template;
    }

    public function setTemplate(Template $template): Transactional
    {
        $this->template = $template;
        return $this;
    }

    public function getSender(): Sender
    {
        return $this->sender;
    }

    public function setSender(Sender $sender): Transactional
    {
        $this->sender = $sender;
        return $this;
    }

    public function getMailer(): Mailer
    {
        return $this->mailer;
    }

    public function setMailer(Mailer $mailer): void
    {
        $this->mailer = $mailer;
    }
}
