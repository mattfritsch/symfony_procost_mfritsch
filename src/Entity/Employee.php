<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas valide.')]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'employees')]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Job $job = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    private ?string $dailyCost = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotBlank(message: 'Ce champ ne peut pas être vide.')]
    private ?\DateTimeImmutable $hiringDate = null;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: ProductionTime::class)]
    private Collection $productionTimes;

    public function __construct()
    {
        $this->productionTimes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): void
    {
        $this->job = $job;
    }

    public function getDailyCost(): ?string
    {
        return $this->dailyCost;
    }

    public function setDailyCost(string $dailyCost): void
    {
        $this->dailyCost = $dailyCost;
    }

    public function getHiringDate(): ?\DateTimeImmutable
    {
        return $this->hiringDate;
    }

    public function setHiringDate(\DateTimeImmutable $hiringDate): void
    {
        $this->hiringDate = $hiringDate;
    }

    /**
     * @return Collection<int, ProductionTime>
     */
    public function getProductionTimes(): Collection
    {
        return $this->productionTimes;
    }

    public function addProductionTime(ProductionTime $productionTime): self
    {
        if (!$this->productionTimes->contains($productionTime)) {
            $this->productionTimes->add($productionTime);
            $productionTime->setEmployee($this);
        }

        return $this;
    }

    public function removeProductionTime(ProductionTime $productionTime): self
    {
        if ($this->productionTimes->removeElement($productionTime)) {
            // set the owning side to null (unless already changed)
            if ($productionTime->getEmployee() === $this) {
                $productionTime->setEmployee(null);
            }
        }

        return $this;
    }
}
