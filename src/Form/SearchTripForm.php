<?php


namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchTripForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'required' => false,
                'class' => Campus::class,
                'choice_label' => 'name',
            ])
            ->add('q', TextType::class, [
                'label' => 'Le nom de la recherche contient :',
                'required' => false,
                'attr' => [
                    'placeholder' => 'rechercher',
                    'class' => 'recherche'
                ],
            ])
            ->add('dateBeginning', DateType::class, [
                'label' => 'Entre',
                'required' => false,
                'empty_data' => '',
                'widget' => "single_text",
            ])
            ->add('dateEnding', DateType::class, [
                'label' => 'et',
                'required' => false,
                'empty_data' => '',
                'widget' => "single_text",
            ])
            ->add('isOrganizer', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur',
                'required' => false,
            ])
            ->add('isRegistered', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit',
                'required' => false,
            ])
            ->add('isNotRegistered', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit',
                'required' => false,
            ])
            ->add('tripPassed', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
            ])
        ;
    }
}