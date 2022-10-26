<?php

declare(strict_types=1);

namespace Mailing\Entity;

use Application\Entity\AbstractEntity;
use Application\Twig\TemplateInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;
use Laminas\Form\Annotation\Attributes;
use Laminas\Form\Annotation\Exclude;
use Laminas\Form\Annotation\Name;
use Laminas\Form\Annotation\Options;
use Laminas\Form\Annotation\Type;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;

#[ORM\Table(name: 'mailing_template')]
#[ORM\Entity]
#[Name(name: 'mailing_template')]
class Template extends AbstractEntity implements TemplateInterface
{
    final public const TEMPLATE_DEFAULT = 1;

    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[Type(type: Hidden::class)]
    private ?int $id = null;

    #[ORM\Column]
    #[Type(type: Text::class)]
    #[Options(options: ['label' => 'txt-mailing-template-name-label', 'help-block' => 'txt-mailing-template-name-help-block'])]
    #[Attributes(attributes: ['placeholder' => 'txt-mailing-template-name-help-placeholder'])]
    private string $name = '';

    #[ORM\Column]
    #[Type(type: Text::class)]
    #[Options(options: [
        'label' => 'txt-mailing-template-subject-label',
        'help-block' => 'txt-mailing-template-subject-help-block',
    ])]
    #[Attributes(attributes: ['placeholder' => 'txt-mailing-template-subject-help-placeholder'])]
    private string $subject = '';

    #[ORM\Column(type: 'text')]
    #[Type(type: Textarea::class)]
    #[Attributes(attributes: ['rows' => '20', 'placeholder' => 'txt-mailing-template-body-placeholder'])]
    #[Options(options: ['label' => 'txt-mailing-template-body-label', 'help-block' => 'txt-mailing-template-body-help-block'])]
    private string $body = '';

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    #[Exclude]
    private DateTime $dateCreated;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    #[Exclude]
    private ?DateTime $lastUpdate = null;

    #[ORM\OneToMany(mappedBy: 'template', targetEntity: Mailing::class, cascade: ['persist'])]
    #[Exclude]
    private Collection $mailing;

    #[ORM\OneToMany(mappedBy: 'template', targetEntity: Transactional::class, cascade: ['persist'])]
    #[Exclude]
    private Collection $transactional;

    #[ORM\OneToMany(mappedBy: 'template', targetEntity: EmailMessage::class, cascade: ['persist'])]
    #[Exclude]
    private Collection $emailMessage;

    #[Pure] public function __construct()
    {
        $this->dateCreated = new DateTime();
        $this->mailing = new ArrayCollection();
        $this->transactional = new ArrayCollection();
        $this->emailMessage = new ArrayCollection();
    }

    public function isDefault(): bool
    {
        return $this->id === self::TEMPLATE_DEFAULT;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody($body): Template
    {
        $this->body = $body;

        return $this;
    }

    public function parseName(): string
    {
        return $this->name;
    }

    public function parseSourceContent(): string
    {
        return $this->body;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): Template
    {
        $this->id = $id;

        return $this;
    }

    public function getMailing(): Collection
    {
        return $this->mailing;
    }

    public function setMailing($mailing): Template
    {
        $this->mailing = $mailing;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject($subject): Template
    {
        $this->subject = $subject;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Template
    {
        $this->name = $name;
        return $this;
    }

    public function getLastUpdate(): ?DateTime
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?DateTime $lastUpdate): Template
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): Template
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getTransactional(): Collection
    {
        return $this->transactional;
    }

    public function setTransactional($transactional): Template
    {
        $this->transactional = $transactional;
        return $this;
    }

    public function getEmailMessage(): Collection
    {
        return $this->emailMessage;
    }

    public function setEmailMessage($emailMessage): Template
    {
        $this->emailMessage = $emailMessage;
        return $this;
    }
}
