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


    public function calculate(Employee $employee, array $deductGroup, int  $deduct = 0)
    {

        $results = [];
        if($employee->getTimeTracks()->count() == 0 )
        {
            return $results;
        }
        $timeTracks = $employee->getTimeTracks();

        $dateTracks = [];
        /** @var TimeTrack $timeTrack */
        foreach ($timeTracks as $timeTrack) {

            if(!array_key_exists($timeTrack->getDate()->format('Y-m-d'), $dateTracks)){
                $dateTracks[$timeTrack->getDate()->format('Y-m-d')] = [];
            }

            if(!in_array($timeTrack->getTimeLog(),$dateTracks[$timeTrack->getDate()->format('Y-m-d')]  ))
            {
                $dateTracks[$timeTrack->getDate()->format('Y-m-d')][] = $timeTrack->getTimeLog();
            }

        }



        if($employee->getDateLogs()->count() == 0)
        {
            foreach ($dateTracks as $date => $val) {

                if(!in_array( $employee->getDepartment(),$deductGroup)){
                    $deduct = 0;
                }

                $results[$date] = $this->calculateByDate($val, $deduct);
            }
        }

        return $results;
    }

    public function calculateByDate(array $timeTracks, int $deduct)
    {
        if (count($timeTracks) == 2) {

            return abs($timeTracks[1]- $timeTracks[0]) / 60 - $deduct   ;
        }
        return 0;
    }




}