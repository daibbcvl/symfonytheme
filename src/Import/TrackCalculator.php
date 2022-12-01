<?php

namespace App\Import;

use App\Entity\Employee;
use App\Entity\TimeTrack;
use Doctrine\ORM\EntityManagerInterface;

class TrackCalculator
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


    public function calculate(array &$overtimeData, Employee $employee, array $deductGroup, string $month, int $deduct = 0)
    {




        $results = [];
        if ($employee->getTimeTracks()->count() == 0) {
            return $results;
        }

        $timeTracks = $employee->getTimeTracks();
        $excludedDates = [];
        $existedDateTracks = $employee->getDateLogs();
        foreach ($existedDateTracks as $existedDateTrack) {
            $excludedDates[] = $existedDateTrack->getDate()->format('Y-m-d');
        }

        //dd($excludedDates);

        $dateTracks = [];
        /** @var TimeTrack $timeTrack */
        foreach ($timeTracks as $timeTrack) {

            if (!array_key_exists($timeTrack->getDate()->format('Y-m-d'), $dateTracks)
            ) {
                $dateTracks[$timeTrack->getDate()->format('Y-m-d')] = [];
            }
            $overtimeData[$timeTrack->getDate()->format('Y-m-d')] = $this->isOvertime($timeTrack->getTimeLog());

            if (!in_array($timeTrack->getTimeLog(), $dateTracks[$timeTrack->getDate()->format('Y-m-d')])
                && !in_array($timeTrack->getDate()->format('Y-m-d'), $excludedDates)
            ) {
                $dateTracks[$timeTrack->getDate()->format('Y-m-d')][] = $timeTrack->getTimeLog();
            }
        }


       // dd($dateTracks);
        //if ($employee->getDateLogs()->count() == 0) {
            foreach ($dateTracks as $date => $val) {

                if (!in_array($employee->getDepartment(), $deductGroup)) {
                    $deduct = 0;
                }

                if($val) {
                    $results[$date] = $this->calculateByDate($val, $deduct);
                }

                //$overtimeData[$date] = $this->isOvertime($val);
            }
        //}

        return $results;
    }

    public function calculateByDate(array $timeTracks, int $deduct)
    {
        if (count($timeTracks) == 2) {

            return abs($timeTracks[1] - $timeTracks[0]) / 60 - $deduct;
        }
        return 0;
    }

    public function isOvertime(int $timestamp)
    {
        $hour = intval( date('H', $timestamp));

        return $hour >=22;

    }


}