<?php

namespace App\Entity;

use App\Config\Categories;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['project:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['project:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['project:read'])]
    private ?string $description = null;

    #[ORM\Column(name: "created_at", type: "datetime", nullable: true)]
    #[Groups(['project:read'])]
    private \DateTime|null $createdAt = null;

    #[ORM\Column(type: Types::FLOAT)]
    #[Groups(['project:read'])]
    private ?float $meta = null;

    /**
     * @var Collection<int, Donation>
     */
    #[ORM\OneToMany(targetEntity: Donation::class, mappedBy: 'project', orphanRemoval: true)]
    #[Groups(['project:donation'])]
    private Collection $donations;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['project:owner'])]
    private ?User $owner = null;

    #[ORM\Column(type: 'string', enumType: Categories::class)]
    #[Groups(['project:read'])]
    private ?Categories $category = null;

    #[ORM\OneToOne(mappedBy: 'project', cascade: ['persist', 'remove'])]
    #[Groups(['project:read'])]
    private ?ProjectDonationStatus $projectDonationStatus = null;

    public function __construct()
    {
        $this->donations = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
    }

    public static function isValidFilterField($filter): bool
    {
        return in_array($filter, ['name', 'createdAt', 'meta', 'category', 'owner']);
    }

    public static function isValidSortField($sort): bool
    {
        return in_array($sort, ['name', 'createdAt', 'meta']);
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt->getTimestamp();
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMeta(): ?float
    {
        return $this->meta;
    }

    public function setMeta(float $meta): static
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return Collection<int, Donation>
     */
    public function getDonations(): Collection
    {
        return $this->donations;
    }

    public function addDonation(Donation $donation): static
    {
        if (!$this->donations->contains($donation)) {
            $this->donations->add($donation);
            $donation->setProject($this);
        }

        return $this;
    }

    public function removeDonation(Donation $donation): static
    {
        if ($this->donations->removeElement($donation)) {
            // set the owning side to null (unless already changed)
            if ($donation->getProject() === $this) {
                $donation->setProject(null);
            }
        }

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getProjectDonationStatus(): ?ProjectDonationStatus
    {
        return $this->projectDonationStatus;
    }

    public function setProjectDonationStatus(ProjectDonationStatus $projectDonationStatus): static
    {
        // set the owning side of the relation if necessary
        if ($projectDonationStatus->getProject() !== $this) {
            $projectDonationStatus->setProject($this);
        }

        $this->projectDonationStatus = $projectDonationStatus;

        return $this;
    }
}
