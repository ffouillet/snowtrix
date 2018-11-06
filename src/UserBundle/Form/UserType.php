<?php

namespace UserBundle\Form;

use UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class,
                array('label' => "Adresse email", 'error_bubbling' => true))
            ->add('username', TextType::class,
                array('label' => "Nom d'utilisateur", 'error_bubbling' => true))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'error_bubbling' => true,
                'invalid_message' => 'Le mot de passe et la confirmation du mot de passe doivent être identiques.',
                'first_options'  => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmation du mot de passe')
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'validation_groups' => array('Default','registration')
        ));
    }
}