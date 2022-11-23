<?php

namespace App\Import;



use PhpOffice\PhpSpreadsheet\IOFactory;

class Reader {


    function getRowCount(string $inputFileName)
    {
       // $spreadsheet = IOFactory::load($inputFileType);

        $spreadsheet = IOFactory::load($inputFileName);
        $rawRows = $spreadsheet->getActiveSheet()->getHighestRow();
        unset($spreadsheet);

        return $rawRows;
    }

    function read(string $inputFileName, $rawRows, int $startRow, int $chunkSize,  string $inputFileType = 'xlsx')
    {
        // Create a new Reader of the type defined in $inputFileType





        // Create a new Instance of our Read Filter
        $chunkFilter = new Chunk();

        $reader = IOFactory::createReader($inputFileType);

        // Tell the Reader that we want to use the Read Filter that we've Instantiated
        $reader->setReadFilter($chunkFilter);
        // Loop to read our worksheet in "chunk size" blocks

            // Tell the Read Filter, the limits on which rows we want to read this iteration
        $chunkFilter->setRows($startRow, $chunkSize);
        // Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
        $spreadsheet = $reader->load($inputFileName);
       dd( $spreadsheet->getActiveSheet()->getRowIterator());

    }
}
