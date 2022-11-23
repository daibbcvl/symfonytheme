<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmployeeRepository::class)
 */
class Employee
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $employeeCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $department;

    /**
     * @ORM\OneToMany(targetEntity=TimeTrack::class, mappedBy="employeeCode", orphanRemoval=true)
     */
    private $timeTracks;

    /**
     * @ORM\OneToMany(targetEntity=DateLog::class, mappedBy="employeeCode", orphanRemoval=true)
     */
    private $dateLogs;

    public function __construct()
    {
        $this->timeTracks = new ArrayCollection();
        $this->dateLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployeeCode(): ?string
    {
        return $this->employeeCode;
    }

    public function setEmployeeCode(string $employeeCode): self
    {
        $this->employeeCode = $employeeCode;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): self
    {
        $this->department = $department;

        return $this;
    }

    /**
     * @return Collection|TimeTrack[]
     */
    public function getTimeTracks(): Collection
    {
        return $this->timeTracks;
    }

    public function addTimeTrack(TimeTrack $timeTrack): self
    {
        if (!$this->timeTracks->contains($timeTrack)) {
            $this->timeTracks[] = $timeTrack;
            $timeTrack->setEmployeeCode($this);
        }

        return $this;
    }

    public function removeTimeTrack(TimeTrack $timeTrack): self
    {
        if ($this->timeTracks->contains($timeTrack)) {
            $this->timeTracks->removeElement($timeTrack);
            // set the owning side to null (unless already changed)
            if ($timeTrack->getEmployeeCode() === $this) {
                $timeTrack->setEmployeeCode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DateLog[]
     */
    public function getDateLogs(): Collection
    {
        return $this->dateLogs;
    }

    public function addDateLog(DateLog $dateLog): self
    {
        if (!$this->dateLogs->contains($dateLog)) {
            $this->dateLogs[] = $dateLog;
            $dateLog->setEmployeeCode($this);
        }

        return $this;
    }

    public function removeDateLog(DateLog $dateLog): self
    {
        if ($this->dateLogs->contains($dateLog)) {
            $this->dateLogs->removeElement($dateLog);
            // set the owning side to null (unless already changed)
            if ($dateLog->getEmployeeCode() === $this) {
                $dateLog->setEmployeeCode(null);
            }
        }

        return $this;
    }
}
