<?php

namespace App\MessageHandler;


use App\Import\ImportManager;
use App\Message\ImportMessage;
use App\Repository\EmployeeRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ImportHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;


    /** @var EmployeeRepository */
    private $employeeRepository;

    /**
     * @var ImportManager
     */
    private $importManager;

    /**
     * @param EmployeeRepository $employeeRepository
     * @param ImportManager $importManager
     */
    public function __construct(EmployeeRepository $employeeRepository, ImportManager $importManager)
    {
        $this->employeeRepository = $employeeRepository;
        $this->importManager = $importManager;
    }


    public function __invoke(ImportMessage $message)
    {
        try {
            $data = $message->getData();
            //var_dump('============ data: ' . $data . ' ===============');

            $rows = unserialize($data);
            $count = count($rows);
            $this->importManager->createEmployeeFomImportData($rows, $this->employeeRepository->getAllCodes());
            $this->importManager->createTimeTrackFromImportData($rows, $this->employeeRepository->findAll());
            var_dump('============ Done! Total exported orders: ' . $count . ' ===============');
        } catch (\Exception $ex) {
            $this->logger->error('============== An error occur while import data to ' . $ex->getMessage() . ', line' . $ex->getTraceAsString() . ' ================');
        }
    }
}
