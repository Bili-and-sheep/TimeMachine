<?php

namespace App\Entity;

use App\Enum\OsFamily;
use App\Repository\OperatingSystemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OperatingSystemRepository::class)]
class OperatingSystem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: OsFamily::class)]
    private ?OsFamily $family = null;

    #[ORM\Column(length: 50)]
    private ?string $version = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFamily(): ?OsFamily
    {
        return $this->family;
    }

    public function setFamily(OsFamily $family): static
    {
        $this->family = $family;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function __toString(): string
    {
        return $this->family?->value . ' ' . $this->version;
    }
}
