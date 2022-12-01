<?php


namespace App\Controller;


use App\Entity\BlogPost;
use App\Entity\Employee;
use App\Entity\Historique;
use App\Entity\OldPost;
use App\Export\TimeTrackExporter;
use App\Form\BlogPostEditFormType;
use App\Form\BlogPostFormType;
use App\Form\ConfigType;
use App\Form\EmployeeDateFormType;
use App\Form\OldPostFormType;
use App\Form\TimeTrackSearchType;
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
use ErrorException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Container\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class TimeTrackController extends BaseController
{
    const WEEDKDAYS = [
        'Sunday' => 'CN',
        'Monday' => 'T2',
        'Tuesday' => 'T3',
        'Wednesday' => 'T4',
        'Thursday' => 'T5',
        'Friday' => 'T6',
        'Saturday' => 'T7'
    ];


    /**
     * @Route("/admin/time_track",name="app_admin_time_track")
     * @IsGranted("ROLE_WRITER")
     */
    public function index(Request $request, DateTrackManager $dateTrackManager, EmployeeRepository $employeeRepository)
    {
        $form = $this->createForm(TimeTrackSearchType::class, null, ['method' => 'GET']);
        $form->handleRequest($request);
        $formData = $form->getData() ?: [];
        $monthStr = isset($formData['month']) ? $formData['month'] : date('m') . '-' . date('Y');
        //dd($monthStr);
        $dates = [];

        $weekdays = self::WEEDKDAYS;
        for ($i = 1; $i <= 31; $i++) {
            $todayString = "{$monthStr}-{$i}";
            $date = \DateTime::createFromFormat('m-Y-d', $todayString);
            $dates[$i] = $weekdays[$date->format('l')];
        }


        $employees = $employeeRepository->findAll();

        $calendars = [];

        foreach ($employees as $employee) {
            $calendars[$employee->getId()] = $dateTrackManager->createCalendarData($employee, $monthStr);
        }


        return $this->render("admin/time/index.html.twig", [
            'employees' => $employees,
            'calendars' => $calendars,
            'dates' => $dates,
            'form' => $form->createView(),
            'month' => $monthStr
        ]);
    }

    /**
     * @Route("/admin/time_track/create",name="app_admin_time_track_create")
     * @IsGranted("ROLE_WRITER")
     */
    public function createTimeTrack(
        Request             $request,
        ImportManager       $importManager,
        EmployeeRepository  $employeeRepository,
        ConfigRepository    $configRepository,
        MessageBusInterface $bus,
        string              $importDir)
    {

        $month = $request->get('month');


        $spreadsheet = $this->createSpread($configRepository, $importDir, $month);

        if ($spreadsheet) {
            $contents = $spreadsheet->getActiveSheet()->toArray();

            $data = serialize($contents);

            $bus->dispatch(new ImportMessage($data));
        } else {
            $this->addFlash('error', 'No data for ' . $month);
            return $this->redirectToRoute('app_admin_time_track');

        }

        $this->addFlash('success', 'Time track was calculated');

        return $this->redirectToRoute('app_admin_time_track');
    }

    /**
     * @Route("/admin/time_track/download",name="app_admin_time_track_download")
     * @IsGranted("ROLE_WRITER")
     */
    public function download(
        Request            $request,
        ImportManager      $importManager,
        EmployeeRepository $employeeRepository,
        ConfigRepository   $configRepository,
        DateTrackManager   $dateTrackManager,
        TimeTrackExporter  $exporter,
        string             $importDir)
    {

        $month = $request->get('month');
        $employees = $employeeRepository->findAll();

        $calendars = [];

        foreach ($employees as $employee) {
            $calendars[$employee->getId()] = $dateTrackManager->createCalendarData($employee, $month);
        }

        //dd($calendars);
        $exporter->export($employees, $calendars);


    }

    private function createSpread($configRepository, $importDir, $thisMonth)
    {
        $configMonth = $configRepository->find(3);
        $importList = unserialize($configMonth->getValue());
        if (!isset($importList[$thisMonth])) {
            return false;
        }
        $fileName = $importList[$thisMonth];
        if (!file_exists($importDir . "/" . $fileName)) {
            return false;
        }
        return IOFactory::load($importDir . "/" . $fileName);
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
