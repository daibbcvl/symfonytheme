<?php


namespace App\Controller;


use App\Form\ConfigType;
use App\Form\Model\ConfigModel;
use App\Repository\CategorieRepository;
use App\Repository\ConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin",name="app_admin_index")
     */
    public function index()
    {
        return $this->render("admin/main.html.twig");
    }


    /**
     * @Route("/admin/config",name="app_admin_config")
     * @IsGranted("ROLE_WRITER")
     */
    public function config(Request $request, string $importDir, EntityManagerInterface $entityManager, ConfigRepository $configRepository)
    {

        $configDeduct = $configRepository->find(1);
        $configDeductGroup = $configRepository->find(2);
        $configMonth = $configRepository->find(3);

        $importList = unserialize($configMonth ->getValue());

        $configDeductModel = ConfigModel::createDBConfig($configDeduct, $configDeductGroup, $configMonth);

       // dd($configDeductModel);
        $today = new \DateTime();
        $configDeductModel->month = $today->format('m-Y');
        // dd($configDeductModel);

        //dd($configDeductModel);
        $form = $this->createForm(ConfigType::class, $configDeductModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uploadedFile = $form->get('fileName')->getData();
            $newFilename = null;
            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $configDeductModel->month . '.' . $uploadedFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $uploadedFile->move(
                        $importDir,
                        $newFilename
                    );
                    //$configDeductModel->fileName = $newFilename;

                } catch (FileException $e) {

                }
            }

            $configDeduct = ConfigModel::mapToDeduct($configDeduct, $form->getData());
            $configDeductGroup = ConfigModel::mapToDeductGroups($configDeductGroup, $form->getData());
            $configMonth = ConfigModel::mapToMonths($configMonth, $form->getData() , $newFilename);
            //dd($configMonth);


            $entityManager->flush();

            $this->addFlash('success', 'Update config successfully.');


            return $this->redirectToRoute('app_admin_index');
        }


        return $this->render("admin/time/config.html.twig", [
            'form' => $form->createView(),
            'importFiles' => $importList

        ]);
    }


}
