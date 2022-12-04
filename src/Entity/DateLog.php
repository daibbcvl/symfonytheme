<?php

namespace App\Entity;

use App\Repository\DateLogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DateLogRepository::class)
 */
class DateLog
{
    const TYPE_DEFAULT_LEAVE = 'NGAY_THUONG';
    const TYPE_ANNUAL_LEAVE = 'PHEP_NAM';
    const TYPE_WEDDING_LEAVE = 'HIEU_HY';
    const TYPE_FUNERAL_LEAVE = 'TANG';
    const TYPE_OTHER_LEAVE = 'KHAC';

    const TYPES = [
        self::TYPE_DEFAULT_LEAVE =>  self::TYPE_DEFAULT_LEAVE,
        self::TYPE_ANNUAL_LEAVE =>  self::TYPE_ANNUAL_LEAVE,
        self::TYPE_WEDDING_LEAVE =>  self::TYPE_WEDDING_LEAVE,
        self::TYPE_FUNERAL_LEAVE =>  self::TYPE_FUNERAL_LEAVE,
        self::TYPE_OTHER_LEAVE =>  self::TYPE_OTHER_LEAVE,
    ];

    const DEDUCT_GROUP = [
        'P.Giám Đốc' => 'P.Giám Đốc',
        'P.HCNS' => 'P.HCNS',
        'P.Kế Toán' =>  'P.Kế Toán',
        'P.Thu Mua' => 'P.Thu Mua',
        'P.XNK' => 'P.XNK',
        'P. Nông Nghiệp' => 'P. Nông Nghiệp',
        'P.Quản lý chất lượng' => 'P.Quản lý chất lượng',
        'QC IQF' => 'QC IQF',
        'COCONUT' => 'COCONUT',
        'BẢO TRÌ' => 'BẢO TRÌ',
        'IQF' => 'IQF',
        'KHO VẬN' => 'KHO VẬN',
        'KHO LẠNH' => 'KHO LẠNH',
        'THỜI VỤ' => 'THỜI VỤ'
    ];

    const DEFAULT_DEDUCT_GROUP = [
        'P.Quản lý chất lượng' => 'P.Quản lý chất lượng',
        'QC IQF' => 'QC IQF',
        'COCONUT' => 'COCONUT',
        'IQF' => 'IQF',
        'THỜI VỤ' => 'THỜI VỤ'
    ];


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
     * @ORM\ManyToOne(targetEntity=Employee::class, inversedBy="dateLogs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $employeeCode;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $type = self::TYPE_DEFAULT_LEAVE;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalMinutes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $overtime = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getTotalMinutes(): ?int
    {
        return $this->totalMinutes;
    }

    public function setTotalMinutes(?int $totalMinutes): self
    {
        $this->totalMinutes = $totalMinutes;

        return $this;
    }

    public function getOvertime(): ?bool
    {
        return $this->overtime;
    }

    public function setOvertime(bool $overtime): self
    {
        $this->overtime = $overtime;

        return $this;
    }
}
