<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileModifierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('email', EmailType::class, [
//                'label' => 'Email '
//            ])
            ->add('nickname', TextType::class, [
                'label' => 'Pseudo '
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom '
            ])
            ->add('lastname', TextType::class, [
                'label'=> 'nom '
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Téléphone ',
                'required' => false
            ])
//            ->add('isActive')
//            ->add('tripsRegistered')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
