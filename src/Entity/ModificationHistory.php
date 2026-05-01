<?php

namespace App\Entity;

use App\Enum\ModificationAction;
use App\Repository\ModificationHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModificationHistoryRepository::class)]
class ModificationHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $date;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'history')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Product $product = null;

    #[ORM\Column(enumType: ModificationAction::class)]
    private ModificationAction $action;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    public function __construct(Product $product, User $user, ModificationAction $action, ?string $comment = null)
    {
        $this->product = $product;
        $this->user    = $user;
        $this->action  = $action;
        $this->comment = $comment;
        $this->date    = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getDate(): \DateTimeImmutable { return $this->date; }

    public function getUser(): ?User { return $this->user; }

    public function getProduct(): ?Product { return $this->product; }

    public function getAction(): ModificationAction { return $this->action; }

    public function getComment(): ?string { return $this->comment; }
}
