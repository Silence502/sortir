<?php

namespace App\Form;

use App\Entity\Place;
use App\Entity\Trip;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie '
            ])
            ->add('dateStartTime', DateTimeType::class, [
                'label' => 'Date et heure de début ',
                'html5' => true,
                'widget' => 'single_text',

            ])
            ->add('duration', TimeType::class, [
                'label' => 'Durée ',
                'input'  => 'datetime',
                'widget' => 'single_text',
            ])
            ->add('registrationDeadline', DateTimeType::class, [
                'label' => 'Date limite d\'inscription ',
                'html5' => true,
                'widget' => 'single_text',

            ])
            ->add('maxRegistrations', IntegerType::class, [
                'label' => 'Nombre de places'
            ])
            ->add('tripInfos', TextareaType::class, [
                'label' => 'Description et infos '
            ])
            ->add('place', EntityType::class, [
                'label' => 'Lieu ',
                'class' => Place::class,
                'choice_label' => 'name'
            ])
            ->add('enregistrer', SubmitType::class)
            ->add('publier_la_sortie', SubmitType::class)
            ->add('supprimer_la_sortie', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}