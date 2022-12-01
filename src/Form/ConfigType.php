<?php


namespace App\Form;


use App\Entity\Config;
use App\Entity\DateLog;
use App\Entity\Role;
use App\Entity\User;
use App\Form\Model\ConfigModel;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('deduct', TextType::class, [
                'constraints' => new NotBlank(),
            ])
            ->add('deductGroups', ChoiceType::class, [
                'choices' => DateLog::DEDUCT_GROUP,
                'multiple' => true,
            ])
            ->add('month', ChoiceType::class, [
                'choices' => ConfigType::createMonthData(),
            ])
            ->add('fileName', FileType::class, [
                'label' => 'Excel file',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                    ])
                ]
            ]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults([
            'data_class' => ConfigModel::class
        ]);
    }

    public static function createMonthData()
    {
        $results = [];

        for ($i = 0; $i <= 12; $i++) {
            $today = new \DateTime();
            $date = $today->modify("-{$i}month");
            $results[$date->format('m-Y')] = $date->format('m-Y');
        }

        return $results;
    }
}
