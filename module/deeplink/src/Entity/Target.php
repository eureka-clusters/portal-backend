<?php

declare(strict_types=1);

namespace Deeplink\Entity;

use Application\Entity\AbstractEntity;
use Deeplink\Entity\Deeplink;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Laminas\Form\Annotation\Attributes;
use Laminas\Form\Annotation\Exclude;
use Laminas\Form\Annotation\Hydrator;
use Laminas\Form\Annotation\Name;
use Laminas\Form\Annotation\Options;
use Laminas\Form\Annotation\Type;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Hydrator\ObjectPropertyHydrator;

#[ORM\Table(name: 'deeplink_target')]
#[ORM\Entity(repositoryClass: \Deeplink\Repository\Target::class)]
#[Hydrator(type: ObjectPropertyHydrator::class)]
#[Name(name: 'deeplink_target')]
class Target extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[Type(type: Hidden::class)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[Type(type: Text::class)]
    #[Options(options: [
        'label'      => 'txt-deeplink-target-target-label',
        'help-block' => 'txt-deeplink-target-target-help-block',
    ])]
    #[Attributes(attributes: ['placeholder' => 'txt-deeplink-target-target-placeholder'])]
    private ?string $target = null;

    #[ORM\Column]
    #[Type(type: Text::class)]
    #[Options(options: [
        'label'      => 'txt-deeplink-target-route-label',
        'help-block' => 'txt-deeplink-target-route-help-block',
    ])]
    #[Attributes(attributes: ['placeholder' => 'txt-deeplink-target-route-placeholder'])]
    private string $route = '';

    #[ORM\OneToMany(mappedBy: 'target', targetEntity: Deeplink::class, cascade: ['persist', 'remove'])]
    #[Exclude]
    private Collection $deeplink;

    #[Pure] public function __construct()
    {
        $this->deeplink = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->target;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Target
    {
        $this->id = $id;
        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): Target
    {
        $this->target = $target;
        return $this;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): Target
    {
        $this->route = $route;
        return $this;
    }

    public function getDeeplink(): ArrayCollection|Collection
    {
        return $this->deeplink;
    }

    public function setDeeplink(ArrayCollection|Collection $deeplink): Target
    {
        $this->deeplink = $deeplink;
        return $this;
    }
}
