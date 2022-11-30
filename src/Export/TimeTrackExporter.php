<?php

namespace App\Export;

use App\Controller\TimeTrackController;
use App\Entity\Employee;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TimeTrackExporter
{


//    public function createExcel($employees, array $callendar, $fileName = 'data.xlsx')
//    {
//        $spreadsheet = new Spreadsheet();
//       // $spreadsheet->setActiveSheetIndex(0);
//
//        $sheet = $spreadsheet->getActiveSheet();
//        //$sheet->setCellValue('A1', 'Mã NV');
//        $this->createHeader($sheet);
//
//
//
//
//        for($line=0; $line <count($employees); $line++)
//        {
//            $total = 0;
//            $sheet->setCellValueByColumnAndRow(1,$line +2, $employees[$line]->getEmployeeCode() );
//            $sheet->setCellValueByColumnAndRow(2,$line +2, $employees[$line]->getName() );
//            $sheet->setCellValueByColumnAndRow(3,$line +2, $employees[$line]->getDepartment());
//            for($j = 1 ; $j <=31; $j++) {
//                $duration = isset($callendar[$employees[$line]->getId()][$j]) ? $callendar[$employees[$line]->getId()][$j]['value'] / 60 : 0;
//                $total += $duration;
//                $sheet->setCellValueByColumnAndRow($j+3,$line +2, $duration);
//
//            }
//            $sheet->setCellValueByColumnAndRow(35,$line +2, $total);
//        }
//
//
//
//
//        $writer = new Xlsx($spreadsheet);
//        $writer->setOffice2003Compatibility(true);
//        setlocale(LC_ALL, 'en_US');
//        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
//        ob_end_clean();
//         $writer->save('php://output');
//
//    }

//    private function createHeader(Worksheet &$sheet)
//    {
//        $cells = [
//            'D1',
//            'E1',
//            'F1',
//            'G1',
//            'H1',
//            'I1',
//            'J1',
//            'K1',
//            'L1',
//            'M1',
//            'N1',
//            'O1',
//            'P1',
//            'Q1',
//            'R1',
//            'S1',
//            'T1',
//            'U1',
//            'V1',
//            'W1',
//            'X1',
//            'Y1',
//            'X1',
//            'AA1',
//            'AB1',
//            'AC1',
//            'AD1',
//            'AE1',
//            'AF1',
//            'AG1',
//            'AH1',
//        ];
//
//
//        $sheet->setCellValue('A1', 'Mã NV');
//        $sheet->setCellValue('B1', 'Tên');
//        $sheet->setCellValue('C1', 'Phòng');
//        $i = 1;
//        foreach ($cells as $cell) {
//            $sheet->setCellValue($cell, $i);
//            $i++;
//        }
//        $sheet->setCellValue('AI1', 'Tổng Cộng');
//    }


    public function export($employees, array $calendars, $fileName = 'data.csv')
    {

        $handle = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
        fwrite($handle, $bom = (\chr(0xEF) . \chr(0xBB) . \chr(0xBF)));
        fputcsv($handle, $this->createHeader());

        for ($line = 0; $line < count($employees); $line++) {
            $row = $this->createRow($employees[$line], $calendars, $line);
            fputcsv($handle, $row);
        }

        //$this->filesystem->writeStream($fileName, $handle);


        fseek($handle, 0);
        // tell the browser it's going to be a csv file
        header('Content-Type: text/csv');
        // tell the browser we want to save it instead of displaying it
        header('Content-Disposition: attachment; filename="' . $fileName . '";');
        // make php send the generated csv lines to the browser
        fpassthru($handle);
        die();

    }


    /**
     * @param Employee $employee
     * @param array $calendars
     * @param int $line
     * @return array
     */
    private function createRow(Employee $employee, array $calendars, int $line)
    {
        $month = date('m');
        $year = date('Y');
        $weekdays = TimeTrackController::WEEDKDAYS;

        $basicData = [
            $employee->getEmployeeCode(),
            $employee->getName(),
            $employee->getDepartment()
        ];

        $monthData = [];
        $totalMinutes = 0;
        $totalAddtion = 0;
        for ($j = 1; $j <= 31; $j++) {
            $duration = 0;
            if (isset($calendars[$employee->getId()][$j])) {

                $duration = $calendars[$employee->getId()][$j]['value'];
                $todayString = "{$year}-{$month}-{$j}";
                $date = \DateTime::createFromFormat('Y-m-d', $todayString);
                if ($weekdays[$date->format('l')] == 'CN') {
                    $totalAddtion += $duration;
                }
                //if($calendars[$employee->getId()][$j])
            }
            $totalMinutes += $duration;
            $monthData[] = $duration;


        }
        $total = $totalMinutes + $totalAddtion;

        $monthData[] = $totalMinutes;
        $monthData[] = $totalAddtion;
        $monthData[] = $total;
        $monthData[] = round($total / 60, 0) . "h:" . ($total % 60) . "m";


        return array_merge($basicData, $monthData);
    }

    private function createHeader()
    {
        return [
            'Mã NV',
            'Tên',
            'Phòng',
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31,
            'Tổng số phút',
            'Tổng số phút cộng thêm',
            'Tổng cộng',
            'Tổng cộng (h)'
        ];
    }
}