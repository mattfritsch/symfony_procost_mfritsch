<?php

namespace App\Entity;

use App\Repository\ProductionTimeRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductionTimeRepository::class)]
class ProductionTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    private ?int $productionTime = null;

    #[ORM\ManyToOne(inversedBy: 'productionTimes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    #[ORM\ManyToOne(inversedBy: 'productionTimes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    private ?Project $project = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $entryDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductionTime(): ?int
    {
        return $this->productionTime;
    }

    public function setProductionTime(int $productionTime): void
    {
        $this->productionTime = $productionTime;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): void
    {
        $this->project = $project;
    }

    public function getEntryDate(): ?\DateTimeImmutable
    {
        return $this->entryDate;
    }

    public function setEntryDate(\DateTimeImmutable $entryDate): void
    {
        $this->entryDate = $entryDate;
    }
}
