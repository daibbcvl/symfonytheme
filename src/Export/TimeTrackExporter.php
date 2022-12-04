<?php

namespace App\Export;

use App\Controller\TimeTrackController;
use App\Entity\DateLog;
use App\Entity\Employee;

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


    public function export($employees, array $calendars, $month, $fileName = 'CongNgay.csv')
    {

        $handle = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
        fwrite($handle, $bom = (\chr(0xEF) . \chr(0xBB) . \chr(0xBF)));
        fputcsv($handle, $this->createHeaderFirst());
        fputcsv($handle, $this->createHeaderSecond($month));


        for ($line = 0; $line < count($employees); $line++) {
            $additionRow = [];
            $leaveRow = [];
            $row = $this->createRow($employees[$line], $calendars, $month, $additionRow, $leaveRow);
            fputcsv($handle, $row);
            fputcsv($handle, $additionRow);
            fputcsv($handle, $leaveRow);

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
    private function createRow(Employee $employee, array $calendars, $monthStr, array &$additionRow, array &$leaveRow)
    {
        $weekdays = TimeTrackController::WEEDKDAYS;

        $basicData = [
            $employee->getEmployeeCode(),
            $employee->getName(),
            $employee->getDepartment()
        ];

        $leaveRow = $additionRow = [
            '',
            '',
            ''
        ];

        $monthData = [];
        $totalMinutes = 0;
        $totalAddition = 0;
        $annualLeave = 0;
        $weddingLeave = 0;
        $funeralLeave = 0;

        for ($j = 1; $j <= 31; $j++) {
            $leaveTemp = null;
            $duration = 0;
            if (isset($calendars[$employee->getId()][$j])) {

                $duration = $calendars[$employee->getId()][$j]['value'];
                $todayString = "{$monthStr}-{$j}";
                $date = \DateTime::createFromFormat('m-Y-d', $todayString);
                if ($weekdays[$date->format('l')] == 'CN') {
                    $totalAddition += $duration;
                }
                //if($calendars[$employee->getId()][$j])


                switch ($calendars[$employee->getId()][$j]['type']) {
                    case DateLog::TYPE_ANNUAL_LEAVE:
                        $annualLeave++;
                        $leaveTemp = 'Phép thường';
                        break;
                    case DateLog::TYPE_WEDDING_LEAVE:
                        $weddingLeave++;
                        $leaveTemp = 'Hiếu hỷ';
                        break;
                    case DateLog::TYPE_FUNERAL_LEAVE:
                        $funeralLeave++;
                        $leaveTemp = 'Phép năm';
                        break;
                }
            }
            $totalMinutes += $duration;


            $hours = $duration / 60;
            $monthData[] = $hours > 8 ? 8 : round($hours, 2);
            $additionRow[] = $hours > 8 ? round($hours, 2) - 8 : null;

            $leaveRow [] = $leaveTemp;
        }
        $total = $totalMinutes + $totalAddition;

//        $monthData[] = $totalMinutes;
        $monthData[] = round($totalAddition / 60, 0) . "h:" . ($totalAddition % 60) . "m";
//        $monthData[] = $total;
        $monthData[] = round($totalMinutes / 60 / 8, 2);

        $monthData[] = $weddingLeave;
        $monthData[] = $annualLeave;
        $monthData[] = $funeralLeave;


        return array_merge($basicData, $monthData);
    }

    private function createHeaderFirst()
    {
        return [
            '',
            '',
            '',
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31,
            '',
        ];
    }

    private function createHeaderSecond($monthStr)
    {
        $arr = [
            'Mã NV',
            'Tên',
            'Phòng',
            // 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31,
            // 'Tổng tăng ca',
        ];


        $weekdays = TimeTrackController::WEEDKDAYS;


        $dates = [];

        for ($i = 1; $i <= 31; $i++) {
            $todayString = "{$monthStr}-{$i}";
            $date = \DateTime::createFromFormat('m-Y-d', $todayString);
            $dates[$i] = $weekdays[$date->format('l')];
        }


        return array_merge($arr, $dates, ['Tổng tăng ca', 'Ngày công thực tế', 'Nghỉ hiếu hỉ', 'Nghỉ phép năm', 'Nghỉ tang']);
    }
}