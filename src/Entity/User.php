<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_UUID', fields: ['uuid'])]
#[UniqueEntity(fields: ['uuid'], message: 'There is already an account with this uuid')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 256)]  // UUIDs are exactly 36 chars in RFC 4122 format
    private ?string $uuid = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'ModifiedByUser')]
    private Collection $products;

    /**
     * @var Collection<int, ModificationHistory>
     */
    #[ORM\ManyToMany(targetEntity: ModificationHistory::class, inversedBy: 'users')]
    private Collection $ModificationHisotry;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->ModificationHisotry = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getUuid(): ?string { return $this->uuid; }

    public function setUuid(string $uuid): static
    {
        $this->uuid = hash('sha256', $uuid);
        return $this;
    }

    public function getUserIdentifier(): string { return (string) $this->uuid; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Nothing to erase — no password is ever stored
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setModifiedByUser($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getModifiedByUser() === $this) {
                $product->setModifiedByUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ModificationHistory>
     */
    public function getModificationHisotry(): Collection
    {
        return $this->ModificationHisotry;
    }

    public function addModificationHisotry(ModificationHistory $modificationHisotry): static
    {
        if (!$this->ModificationHisotry->contains($modificationHisotry)) {
            $this->ModificationHisotry->add($modificationHisotry);
        }

        return $this;
    }

    public function removeModificationHisotry(ModificationHistory $modificationHisotry): static
    {
        $this->ModificationHisotry->removeElement($modificationHisotry);

        return $this;
    }
}
