<?php


namespace App\Controller;


use App\Entity\BlogPost;
use App\Entity\Employee;
use App\Entity\Historique;
use App\Entity\OldPost;
use App\Form\BlogPostEditFormType;
use App\Form\BlogPostFormType;
use App\Form\ConfigType;
use App\Form\EmployeeDateFormType;
use App\Form\OldPostFormType;
use App\Import\DateTrackManager;
use App\Import\ImportManager;
use App\Import\Reader;
use App\Import\TrackCalculator;
use App\Message\ImportMessage;
use App\Repository\BlogPostRepository;
use App\Repository\ConfigRepository;
use App\Repository\EmployeeRepository;
use App\Repository\HistoriqueRepository;
use App\Services\UploadHelper;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Container\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class TimeTrackController extends BaseController
{


    /**
     * @Route("/admin/time_track",name="app_admin_time_track")
     * @IsGranted("ROLE_WRITER")
     */
    public function index(ImportManager $importManager, DateTrackManager $dateTrackManager, EmployeeRepository $employeeRepository, string $importDir)
    {

        $employees = $employeeRepository->findAll();

        $calendars = [];
        foreach ($employees as $employee) {
            $calendars[$employee->getId()] = $dateTrackManager->createCalendarData($employee);
        }


        return $this->render("admin/time/index.html.twig", ['employees' => $employees, 'calendars' => $calendars]);
    }

    /**
     * @Route("/admin/time_track/create",name="app_admin_time_track_create")
     * @IsGranted("ROLE_WRITER")
     */
    public function createTimeTrack(
        ImportManager       $importManager,
        EmployeeRepository  $employeeRepository,
        MessageBusInterface $bus,

        string              $importDir)
    {

//        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
//        $reader->setReadDataOnly(true);
//        $spreadsheet = $reader->load($importDir."/chamcong.xlsx");
//
//       // $output = shell_exec("ls -lart /application/public/uploads");
//        //echo "<pre>$output</pre>";
//
//        dd($spreadsheet);

//        $inputFileName = $importDir . "/chamcong.xlsx";
//
//
//        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
//
//        $rowCounts = $reader->getRowCount($importDir . "/chamcong.xlsx");
//
//
//        $chunkSize =  100;
//
//        $contents = $reader->read($inputFileName, $rowCounts,101, $chunkSize, $inputFileType);
//
//        dd($contents);
//
//
//        for ($startRow = 1; $startRow <= $rowCounts; $startRow += $chunkSize) {
//
//            $contents = $reader->read($inputFileName, $rowCounts,$startRow, $chunkSize, $inputFileType);
//            $importManager->createEmployeeFomImportData($contents, $employeeRepository->getAllCodes());
//            $importManager->createTimeTrackFromImportData($contents, $employeeRepository->findAll());
//            //$this->addFlash('success', 'Mapped Data');
//
//        }

        $spreadsheet = IOFactory::load($importDir . "/11-2022.xlsx");

        if (!empty($spreadsheet)) {
            $contents = $spreadsheet->getActiveSheet()->toArray();

            $data = serialize($contents);

            // dd($data);


            $bus->dispatch(new ImportMessage($data));


            // $importManager->createEmployeeFomImportData($contents, $employeeRepository->getAllCodes());
            //$importManager->createTimeTrackFromImportData($contents, $employeeRepository->findAll());
            //$this->addFlash('success', 'Mapped Data');
        } else {
            //$this->invalidReq('Excel file type is invalid.');
        }

        $this->addFlash('success', 'Time track was calculated');

        return $this->redirectToRoute('app_admin_time_track');

        // return $this->render("admin/time/track.html.twig", []);
    }

    /**
     * @Route("/admin/date_track/bulk",name="app_admin_date_track_bulk")
     * @IsGranted("ROLE_WRITER")
     */
    public function calculateDateLog(
        EmployeeRepository $employeeRepository,
        ConfigRepository   $configRepository,
        TrackCalculator    $trackCalculator,
        DateTrackManager   $dateTrackManager): \Symfony\Component\HttpFoundation\RedirectResponse
    {

        ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
        set_time_limit(300);

        $config = $configRepository->find(1);
        $configDeductGroup = $configRepository->find(2);
        $deductGroups = unserialize($configDeductGroup->getValue());
        $deductGroups = is_array($deductGroups) ? $deductGroups : [];


        $employees = $employeeRepository->findAll();
        foreach ($employees as $employee) {
            if ($employee->getDateLogs()->count() == 0) {
                $overtimeData = [];
                $dateTrackData = $trackCalculator->calculate($overtimeData, $employee, $deductGroups, intval($config->getValue()));
                if ($dateTrackData) {
                    $dateTrackManager->bulk($employee, $dateTrackData, $overtimeData);
                }

            }
        }
        $this->addFlash('success', 'Date log was calculated');

        return $this->redirectToRoute('app_admin_time_track');
    }

//    /**
//     * @Route("/admin/time_track/employee/{id}",name="app_admin_time_track_employeee")
//     * @IsGranted("ROLE_WRITER")
//     */
//    public function viewEmployee(Request $request, ImportManager $importManager, EmployeeRepository $employeeRepository, Employee $employee)
//    {
//        $form = $this->createForm(EmployeeDateFormType::class);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $entityManager->persist($form->getData());
//            $entityManager->flush();
//
//            $this->addFlash('success', 'Create sector successfully.');
//            if ($url = $request->get('redirect_url')) {
//                return $this->redirect($url);
//            }
//
//            return $this->redirectToRoute('sector_create');
//        }
//
//
//        return $this->render("admin/time/employee.html.twig", [
//            'form' => $form->createView(),
//
//        ]);
//    }


}
