<?php

namespace App\Entity;

use App\Enum\SubmissionStatus;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ProductName = null;

    #[ORM\Column(length: 255)]
    private ?string $TechnicalName = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $ReleaseDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $DiscontinuedYear = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Description = null;

    #[ORM\Column]
    private ?int $OriginalPrice = null;

#[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Sources = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductType $ProductType = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?OperatingSystem $LaunchOS = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?OperatingSystem $LastSupportedOS = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ModifiedByUser = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ModificationHistory $ModificationHistory = null;

    #[ORM\Column(enumType: SubmissionStatus::class)]
    private SubmissionStatus $status = SubmissionStatus::Pending;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $rejectionComment = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $options = null;

    /**
     * @var Collection<int, ProductImage>
     */
    #[ORM\OneToMany(targetEntity: ProductImage::class, mappedBy: 'product', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $images;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'products')]
    private Collection $tags;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->ProductName;
    }

    public function setProductName(string $ProductName): static
    {
        $this->ProductName = $ProductName;

        return $this;
    }

    public function getTechnicalName(): ?string
    {
        return $this->TechnicalName;
    }

    public function setTechnicalName(string $TechnicalName): static
    {
        $this->TechnicalName = $TechnicalName;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeImmutable
    {
        return $this->ReleaseDate;
    }

    public function setReleaseDate(?\DateTimeImmutable $ReleaseDate): static
    {
        $this->ReleaseDate = $ReleaseDate;

        return $this;
    }

    public function getDiscontinuedYear(): ?\DateTimeImmutable
    {
        return $this->DiscontinuedYear;
    }

    public function setDiscontinuedYear(?\DateTimeImmutable $DiscontinuedYear): static
    {
        $this->DiscontinuedYear = $DiscontinuedYear;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getOriginalPrice(): ?int
    {
        return $this->OriginalPrice;
    }

    public function setOriginalPrice(int $OriginalPrice): static
    {
        $this->OriginalPrice = $OriginalPrice;

        return $this;
    }

    public function getInflationAdjustedPrice(): ?int
    {
        if ($this->OriginalPrice === null || $this->ReleaseDate === null) {
            return null;
        }

        $releaseYear  = (int) $this->ReleaseDate->format('Y');
        $currentYear  = (int) (new \DateTimeImmutable())->format('Y');
        $years        = max(0, $currentYear - $releaseYear);

        return (int) round($this->OriginalPrice * (1.03 ** $years));
    }

    public function getSources(): ?string
    {
        return $this->Sources;
    }

    public function setSources(?string $Sources): static
    {
        $this->Sources = $Sources;

        return $this;
    }

    public function getProductType(): ?ProductType
    {
        return $this->ProductType;
    }

    public function setProductType(?ProductType $ProductType): static
    {
        $this->ProductType = $ProductType;

        return $this;
    }

    public function getLaunchOS(): ?OperatingSystem
    {
        return $this->LaunchOS;
    }

    public function setLaunchOS(?OperatingSystem $LaunchOS): static
    {
        $this->LaunchOS = $LaunchOS;

        return $this;
    }

    public function getLastSupportedOS(): ?OperatingSystem
    {
        return $this->LastSupportedOS;
    }

    public function setLastSupportedOS(?OperatingSystem $LastSupportedOS): static
    {
        $this->LastSupportedOS = $LastSupportedOS;

        return $this;
    }

    public function getModifiedByUser(): ?User
    {
        return $this->ModifiedByUser;
    }

    public function setModifiedByUser(?User $ModifiedByUser): static
    {
        $this->ModifiedByUser = $ModifiedByUser;

        return $this;
    }

    public function getModificationHistory(): ?ModificationHistory
    {
        return $this->ModificationHistory;
    }

    public function setModificationHistory(?ModificationHistory $ModificationHistory): static
    {
        $this->ModificationHistory = $ModificationHistory;

        return $this;
    }

    public function getStatus(): SubmissionStatus
    {
        return $this->status;
    }

    public function setStatus(SubmissionStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getRejectionComment(): ?string
    {
        return $this->rejectionComment;
    }

    public function setRejectionComment(?string $rejectionComment): static
    {
        $this->rejectionComment = $rejectionComment;

        return $this;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(?array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return Collection<int, ProductImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ProductImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(ProductImage $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
