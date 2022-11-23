<?php

namespace App\Entity;

use App\Repository\TimeTrackRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TimeTrackRepository::class)
 */
class TimeTrack
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Employee::class, inversedBy="timeTracks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $employeeCode;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $timeLog;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setEnd(int $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEmployeeCode(): ?Employee
    {
        return $this->employeeCode;
    }

    public function setEmployeeCode(?Employee $employeeCode): self
    {
        $this->employeeCode = $employeeCode;

        return $this;
    }

    public function getTimeLog(): ?int
    {
        return $this->timeLog;
    }

    public function setTimeLog(?int $timeLog): self
    {
        $this->timeLog = $timeLog;

        return $this;
    }
}
