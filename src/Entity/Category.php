<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: EventRepository::class)]
class Category
{
    #[Id, GeneratedValue, Column]
    private ?int $id = null;

    #[Column(length: 100)]
    #[Assert\NotBlank]
    private string $name = '';

    #[Column(length: 120, unique: true)]
    private string $slug = '';

    #[OneToMany(mappedBy: 'category', targetEntity: Event::class)]
    private iterable $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $v): self
    {
        $this->name = $v;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $v): self
    {
        $this->slug = $v;
        return $this;
    }
}
