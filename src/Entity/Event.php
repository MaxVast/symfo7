<?php
declare(strict_types=1);

namespace App\Entity;

use App\Enum\EventStatus;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Index(columns: ['start_at'], name: 'idx_event_start_at')]
class Event
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank, Assert\Length(min: 5, max: 180)]
    private string $title = '';

    #[ORM\Column(length: 200, unique: true)]
    private string $slug = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $description = '';

    #[ORM\Column]
    private \DateTimeImmutable $startAt;

    #[ORM\Column]
    private \DateTimeImmutable $endAt;

    #[ORM\Column]
    #[Assert\Positive]
    private int $capacity = 20;

    #[ORM\Column(enumType: EventStatus::class)]
    private EventStatus $status = EventStatus::Draft;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverImage = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'organizedEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organizer = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Registration::class, orphanRemoval: true)]
    private Collection $registrations;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->registrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $v): self
    {
        $this->title = $v;
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $v): self
    {
        $this->description = $v;
        return $this;
    }

    public function getStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $v): self
    {
        $this->startAt = $v;
        return $this;
    }

    public function getEndAt(): \DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $v): self
    {
        $this->endAt = $v;
        return $this;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function setCapacity(int $v): self
    {
        $this->capacity = $v;
        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(?string $v): self
    {
        $this->coverImage = $v;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(User $v): self
    {
        $this->organizer = $v;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $v): self
    {
        $this->category = $v;
        return $this;
    }

    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function getRemainingPlaces(): int
    {
        return max(0, $this->capacity - $this->getConfirmedCount());
    }

    public function getConfirmedCount(): int
    {
        return $this->registrations->filter(fn(Registration $r) => $r->getStatus() === \App\Enum\RegistrationStatus::Confirmed)->count();
    }

    public function getStatus(): EventStatus
    {
        return $this->status;
    }

    public function setStatus(EventStatus $v): self
    {
        $this->status = $v;
        return $this;
    }

    public function isPublished(): bool
    {
        return $this->status === EventStatus::Published;
    }

    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if ($this->endAt <= $this->startAt)
            $context->buildViolation('La date de fin doit être postérieure à la date de début.')->atPath('endAt')->addViolation();
    }
}
