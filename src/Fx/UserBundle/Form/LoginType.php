<?php

namespace Fx\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', TextType::class,
                array(
                    'label' => "Nom d'utilisateur ou adresse email",
                    'required' => false,
                    'constraints' => array(
                        new NotBlank(
                            array('message' => "Le nom d'utilisateur ou l'adresse email que vous avez saisi ne correspondent à aucun utilisateur.")))
            ))
            ->add('_password', PasswordType::class,
                array(
                    'label' => "Mot de passe",
                    'required' => false,
                    'constraints' => array(
                        new NotBlank(
                            array('message' => "Le mot de passe que avez saisi est erroné.")))
                ));
    }

}