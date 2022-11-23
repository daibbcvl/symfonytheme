<?php


namespace App\Form;


use App\Entity\DateLog;
use App\Entity\Employee;
use App\Entity\Role;
use App\Entity\User;
use App\Form\Model\DateLogEditModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DateLogEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

//        $builder
//            ->add('totalMinutes', NumberType::class, [])
//
//
//            ->add('type', ChoiceType::class, [
//                'choices' => DateLog::TYPES,
//            ])
//            ->add('note', TextareaType::class, []);


        $builder
            ->add('hour', NumberType::class, [])
            ->add('minute', NumberType::class, [])

            ->add('type', ChoiceType::class, [
                'choices' => DateLog::TYPES,
            ])
            ->add('note', TextareaType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults([
            'data_class' => DateLogEditModel::class
        ]);
    }
}
