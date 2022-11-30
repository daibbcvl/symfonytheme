<?php


namespace App\Controller;


use App\Entity\BlogPost;
use App\Entity\DateLog;
use App\Entity\Employee;
use App\Entity\Historique;
use App\Entity\OldPost;
use App\Form\BlogPostEditFormType;
use App\Form\BlogPostFormType;
use App\Form\DateLogEditFormType;
use App\Form\EmployeeDateFormType;
use App\Form\Model\DateLogEditModel;
use App\Form\OldPostFormType;
use App\Import\DateTrackManager;
use App\Import\ImportManager;
use App\Import\TrackCalculator;
use App\Repository\BlogPostRepository;
use App\Repository\ConfigRepository;
use App\Repository\EmployeeRepository;
use App\Repository\HistoriqueRepository;
use App\Repository\TimeTrackRepository;
use App\Services\UploadHelper;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Container\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DateLogController extends BaseController
{


    /**
     * @Route("/admin/date_log/employee/{id}",name="app_admin_date_log_employee_index")
     * @IsGranted("ROLE_WRITER")
     */
    public function viewEmployee(Employee $employee)
    {

        return $this->render("admin/date_log/index.html.twig", ['employee' => $employee]);

    }


    /**
     * @Route("/admin/date_log/edit/{id}",name="app_admin_date_log_edit")
     * @IsGranted("ROLE_WRITER")
     */
    public function edit(Request $request, DateLog $dateLog, TimeTrackRepository $timeTrackRepository, EntityManagerInterface $entityManager)
    {

        $timeTracks = $timeTrackRepository->findBy(['date' => $dateLog->getDate(), 'employeeCode' => $dateLog->getEmployeeCode()]);
        $dateLogModel = new DateLogEditModel();
        $dateLogModel = $dateLogModel->createFromDateLog($dateLog, $dateLogModel);

        $form = $this->createForm(DateLogEditFormType::class, $dateLogModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $dateLog = DateLogEditModel::convertToDateLog($dateLog, $dateLogModel);

            $entityManager->flush();
            $this->addFlash('success', 'Edit Date log successfully.');

            if ($url = $request->get('app_admin_date_log_employee_index')) {
                return $this->redirect($url);
            }
        }


        return $this->render("admin/date_log/index.html.twig", [
            'dateLog' => $dateLog,
            'timeTracks' => $timeTracks,
            'form' => $form->createView(),
        ]);

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


}
