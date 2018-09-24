<?php

namespace Fx\UserBundle\Form;

use Fx\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('currentPlainPassword', PasswordType::class, array(
                'mapped' => false,
                'error_bubbling' => true,
                'label' => 'Mot de passe actuel',
                'required' => false,
                'constraints' => array(new UserPassword(array('message' => 'Votre mot de passe actuel est incorrect.')))
            ))

            ->add('plainPassword', RepeatedType::class, array(
                'mapped' => false,
                'type' => PasswordType::class,
                'required' => false,
                'error_bubbling' => true,
                'invalid_message' => 'Le nouveau mot de passe et la confirmation du nouveau mot de passe doivent être identiques.',
                'first_options'  => array('label' => 'Nouveau mot de passe'),
                'second_options' => array('label' => 'Confirmation du nouveau mot de passe'),
                'constraints' => array(new NotBlank(array('message' => 'Votre nouveau mot de passe ne doit pas être vide.')))
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'validation_groups' => array('Default', 'change_password')
        ));
    }

}