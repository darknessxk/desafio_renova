<?php

namespace App\Entity;

use App\Config\ProjectStatus;
use App\Repository\ProjectDonationStatusRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProjectDonationStatusRepository::class)]
#[ORM\Table(name: 'project_donation_status')]
class ProjectDonationStatus
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    #[Groups(['project_status:read', 'project:status'])]
    private int $id;

    #[ORM\Column(type: 'integer')]
    #[Groups(['project_status:read', 'project:status'])]
    private int $donationCount;

    #[ORM\Column(type: Types::FLOAT)]
    #[Groups(['project_status:read', 'project:status'])]
    private float $donationTotal;

    #[ORM\OneToOne(inversedBy: 'projectDonationStatus', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['project_status:project'])]
    private ?Project $project = null;

    #[Groups(['project_status:read', 'project:status'])]
    private ?float $percentage = null;

    #[Groups(['project_status:read', 'project:status'])]
    private ?ProjectStatus $status = null;

    public function __construct()
    {
        $this->donationCount = 0;
        $this->donationTotal = 0.0;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDonationCount(): int
    {
        return $this->donationCount;
    }

    public function setDonationCount(int $donationCount): void
    {
        $this->donationCount = $donationCount;
    }

    public function getDonationTotal(): float
    {
        return $this->donationTotal;
    }

    public function setDonationTotal(float $donationTotal): void
    {
        $this->donationTotal = $donationTotal;
    }

    public function getPercentage(): float
    {
        return $this->donationTotal / $this->getProject()->getMeta() * 100;
    }

    public function getStatus(): ProjectStatus
    {
        if ($this->donationTotal >= $this->getProject()->getMeta()) {
            return ProjectStatus::Completed;
        }

        return ProjectStatus::Open;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}