<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Place;
use App\Entity\Trip;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CancelTripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie ',
                'disabled' => true
            ])
            ->add('dateStartTime', DateTimeType::class, [
                'label' => 'Date et heure de début ',
                'html5' => true,
                'disabled' => true
            ])
            ->add('campusOrganizer', EntityType::class, [
                'label' => 'Campus ',
                'class' => Campus::class,
                'choice_label' => 'name',
                'disabled' => true
            ])
            ->add('place', EntityType::class, [
                'label' => 'Lieu ',
                'class' => Place::class,
                'choice_label' => 'name',
                'disabled' => true
            ])
            ->add('tripInfos', TextareaType::class, [
                'label' => 'Motif '
            ])
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
