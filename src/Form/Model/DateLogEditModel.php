<?php

namespace App\Form\Model;

use App\Entity\DateLog;

class DateLogEditModel
{
    public ?string $type;
    public ?string $note;
    public ?int $minute;
    public ?int $hour;
    public bool $overtime;

    public function createFromDateLog(DateLog $dateLog, DateLogEditModel $dateLogEditModel)
    {
        $dateLogEditModel->type = $dateLog->getType();
        $dateLogEditModel->note = $dateLog->getNote();
        $dateLogEditModel->hour = floor($dateLog->getTotalMinutes() / 60);
        $dateLogEditModel->minute = $dateLog->getTotalMinutes() % 60;
        $dateLogEditModel->overtime = $dateLog->getOvertime();

        return $dateLogEditModel;
    }

    public static function convertToDateLog(DateLog $dateLog, DateLogEditModel $dateLogEditModel)
    {
        $dateLog->setType($dateLogEditModel->type);
        $dateLog->setNote($dateLogEditModel->note);
        $dateLog->setTotalMinutes($dateLogEditModel->hour * 60 + $dateLogEditModel->minute );
        $dateLog->setOvertime($dateLogEditModel->overtime);
        return $dateLog;
    }
}