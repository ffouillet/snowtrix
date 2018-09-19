<?php

namespace Fx\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_usernameOrEmail', TextType::class, array('label' => "Nom d'utilisateur ou adresse email"))
            ->add('_password', PasswordType::class, array('label' => "Mot de passe"));
    }

}