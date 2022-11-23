<?php


namespace App\Controller;


use App\Form\ConfigType;
use App\Form\Model\ConfigModel;
use App\Repository\CategorieRepository;
use App\Repository\ConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin",name="app_admin_index")
     */
    public function index(){
        return $this->render("admin/main.html.twig");
    }


    /**
     * @Route("/admin/config",name="app_admin_config")
     * @IsGranted("ROLE_WRITER")
     */
    public function config(Request $request, EntityManagerInterface $entityManager, ConfigRepository $configRepository)
    {

        $configDeduct = $configRepository->find(1);
        $configDeductGroup = $configRepository->find(2);

        $configDeductModel = ConfigModel::createDBConfig($configDeduct, $configDeductGroup);



        $form = $this->createForm(ConfigType::class, $configDeductModel) ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $configDeduct = ConfigModel::mapToDeduct($configDeduct, $form->getData());
            $configDeductGroup = ConfigModel::mapToDeductGroups($configDeductGroup, $form->getData());


            $entityManager->flush();

            $this->addFlash('success', 'Update config successfully.');


            return $this->redirectToRoute('app_admin_index');
        }


        return $this->render("admin/time/config.html.twig", [
            'form' => $form->createView(),

        ]);
    }


}
