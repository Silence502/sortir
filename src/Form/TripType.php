<?php

namespace App\Form;

use App\Entity\Place;
use App\Entity\Trip;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('dateStartTime', DateTimeType::class, [
                'label' => 'Date et heure de début',
                'html5' => true,
            ])
            ->add('duration', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
            ])
            ->add('registrationDeadline')
            ->add('maxRegistrations')
            ->add('tripInfos')
            ->add('place', EntityType::class, [
                'label' => 'Lieu',
                'class' => Place::class,
                'choice_label' => 'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}