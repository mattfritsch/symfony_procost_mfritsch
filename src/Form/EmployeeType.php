<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Job;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('email', TextType::class, [
                'label' => 'Email'
            ])
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'label' => 'Métier',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('j')
                        ->orderBy('j.name', 'ASC');
                },
                'choice_label' => 'name'
            ])
            ->add('dailyCost', NumberType::class, [
                'label' => 'Coût journalier (en €)',
                'html5' => true
            ])
            ->add('hiringDate', DateType::class, [
                'label' => 'Date d\'embauche',
                'html5' => true,
                'input' => 'datetime_immutable'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
