<?php

namespace App\Import;

use App\Entity\Employee;
use App\Entity\TimeTrack;
use Doctrine\ORM\EntityManagerInterface;

class ImportManager
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


    public function createEmployeeFomImportData(array $rows, array $employeeCodes)
    {
        try{
            $i = 0;
            $insertedCodes = [];
            foreach ($rows as $row) {
                if ($i == 0) {
                    $i++;
                    continue;
                }
                if (!in_array($row[1], $employeeCodes) && !in_array($row[1], $insertedCodes)) {


                    $employee = new Employee();
                    $employee->setEmployeeCode($row[1]);
                    $employee->setName($row[2]);
                    $employee->setDepartment($row[3]);
                    $this->em->persist($employee);

                    $insertedCodes[] = $row[1];

                }
                if ($i % 10 == 0) {
                    $this->em->flush();;
                }

                //dd($employee);

                $i++;
            }
            $this->em->flush();;
        }
        catch (\Exception $exception)
        {

            dd($exception);
        }

       // dd($rows);


    }

    public function createTimeTrackFromImportData(array $rows, array $employees)
    {

        // IMPORTANT - Disable the Doctrine SQL Logger
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);


        $i = 0;
        $tempObjets = [];
        foreach ($rows as $row) {
            if ($i == 0) {
                $i++;
                continue;
            }

            //$trackedDate = \DateTime::createFromFormat('Y-m-d', "2018-09-09"))
            $trackedDate = \DateTime::createFromFormat('m/d/Y', $row[4]);


            $timeTrack = new TimeTrack();
            $timeTrack->setEmployeeCode($this->findEmployeeByCode($employees, $row[1]));
            $timeTrack->setDate($trackedDate);
            $timeTrack->setTimeLog(strtotime($row[4] . " " . $row[6]));
            $this->em->persist($timeTrack);
            $tempObjets[] = $timeTrack;

            for ($j = 7; $j <= 15; $j++) {
                if ($row[$j]) {
                    $timeTrackNext = new TimeTrack();
                    $timeTrackNext->setEmployeeCode($this->findEmployeeByCode($employees, $row[1]));
                    $timeTrackNext->setDate($trackedDate);
                    $timeTrackNext->setTimeLog(strtotime($row[4] . " " . $row[$j]));
                    $this->em->persist($timeTrackNext);
                    $tempObjets[] = $timeTrackNext;
                }
            }

            if ($i % 500 == 0) {
                $this->em->flush();

                // IMPORTANT - clean entities
                foreach($tempObjets as $tempObject) {
                    $this->em->detach($tempObject);
                }

                $tempObjets = null;
                gc_enable();
                gc_collect_cycles();
            }

            $i++;
        }
        $this->em->flush();;
    }

    /**
     * @param array $employees
     * @param string $code
     * @return Employee
     */
    private function findEmployeeByCode(array $employees, string $code)
    {
        /** @var Employee $employee */
        foreach ($employees as $employee) {
            if ($employee->getEmployeeCode() == $code) {
                return $employee;
            }
        }

        $employee = new Employee();
        $employee->setEmployeeCode($code);

        return $employee;
    }

}