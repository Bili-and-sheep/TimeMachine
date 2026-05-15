<?php

namespace App\Entity;

use App\Enum\SubmissionStatus;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Product name is required.')]
    #[Assert\Length(max: 255)]
    private ?string $productName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Technical name is required.')]
    #[Assert\Length(max: 255)]
    private ?string $technicalName = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $releaseDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $discontinuedYear = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Price is required.')]
    #[Assert\Positive(message: 'Price must be a positive integer.')]
    private ?int $originalPrice = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sources = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductType $productType = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?OperatingSystem $launchOS = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?OperatingSystem $lastSupportedOS = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $modifiedByUser = null;

    /**
     * @var Collection<int, ModificationHistory>
     */
    #[ORM\OneToMany(targetEntity: ModificationHistory::class, mappedBy: 'product', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $history;

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
        $this->images  = new ArrayCollection();
        $this->tags    = new ArrayCollection();
        $this->history = new ArrayCollection();
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
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }

    public function getTechnicalName(): ?string
    {
        return $this->technicalName;
    }

    public function setTechnicalName(string $technicalName): static
    {
        $this->technicalName = $technicalName;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeImmutable
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeImmutable $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getDiscontinuedYear(): ?\DateTimeImmutable
    {
        return $this->discontinuedYear;
    }

    public function setDiscontinuedYear(?\DateTimeImmutable $discontinuedYear): static
    {
        $this->discontinuedYear = $discontinuedYear;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOriginalPrice(): ?int
    {
        return $this->originalPrice;
    }

    public function setOriginalPrice(int $originalPrice): static
    {
        $this->originalPrice = $originalPrice;

        return $this;
    }

    public function getSources(): ?string
    {
        return $this->sources;
    }

    public function setSources(?string $sources): static
    {
        $this->sources = $sources;

        return $this;
    }

    public function getProductType(): ?ProductType
    {
        return $this->productType;
    }

    public function setProductType(?ProductType $productType): static
    {
        $this->productType = $productType;

        return $this;
    }

    public function getLaunchOS(): ?OperatingSystem
    {
        return $this->launchOS;
    }

    public function setLaunchOS(?OperatingSystem $launchOS): static
    {
        $this->launchOS = $launchOS;

        return $this;
    }

    public function getLastSupportedOS(): ?OperatingSystem
    {
        return $this->lastSupportedOS;
    }

    public function setLastSupportedOS(?OperatingSystem $lastSupportedOS): static
    {
        $this->lastSupportedOS = $lastSupportedOS;

        return $this;
    }

    public function getModifiedByUser(): ?User
    {
        return $this->modifiedByUser;
    }

    public function setModifiedByUser(?User $modifiedByUser): static
    {
        $this->modifiedByUser = $modifiedByUser;

        return $this;
    }

    /** @return Collection<int, ModificationHistory> */
    public function getHistory(): Collection
    {
        return $this->history;
    }

    public function addHistory(ModificationHistory $entry): static
    {
        if (!$this->history->contains($entry)) {
            $this->history->add($entry);
        }

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
