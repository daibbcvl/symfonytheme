<?php

namespace App\Import;

use App\Entity\DateLog;
use App\Entity\Employee;
use App\Entity\TimeTrack;
use Doctrine\ORM\EntityManagerInterface;

class DateTrackManager
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function bulk(Employee $employee, array $dateTrackData, array $overtimeData)
    {
        foreach ($dateTrackData as $dt => $totalHours)
        {
            $trackedDate = \DateTime::createFromFormat('Y-m-d', $dt);
            $dateTrack = new DateLog();
            $dateTrack->setEmployeeCode($employee);
            $dateTrack->setDate($trackedDate);
            $dateTrack->setTotalMinutes($totalHours);
            $dateTrack->setOvertime($overtimeData[$dt]);
            $this->em->persist($dateTrack);

        }

        $this->em->flush();
    }


    public function createCalendarData(Employee $employee, $monthStr)
    {
        //dd($monthStr);
        $result = [];
        for($i = 1; $i<=31; $i++)
        {
            $result[$i] =  null;
            foreach ($employee->getDateLogs() as $dateLog)
            {
                if($dateLog->getDate()->format("d-m-Y") == str_pad($i, 2, '0', STR_PAD_LEFT).'-'.$monthStr)
                {
                    $result[$i]["value"] = $dateLog->getTotalMinutes();
                    $result[$i]["id"] = $dateLog->getId();
                    $result[$i]["overtime"] = $dateLog->getOvertime();
                    $result[$i]["type"] = $dateLog->getType();
                }
            }
        }

        return $result;
    }


}