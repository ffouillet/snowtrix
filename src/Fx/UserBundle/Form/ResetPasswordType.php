<?php

namespace Fx\UserBundle\Form;

use Fx\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'error_bubbling' => true,
                'required' => true,
                'invalid_message' => 'Le mot de passe et la confirmation du mot de passe doivent être identiques.',
                'first_options'  => array('label' => 'Nouveau mot de passe'),
                'second_options' => array('label' => 'Confirmation du nouveau mot de passe')
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }

}