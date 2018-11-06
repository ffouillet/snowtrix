<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ForgottenPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userEmailOrUsername', TextType::class,
                array('label' => "Nom d'utilisateur ou adresse email",
                    'required' => true,
                    'error_bubbling' => true,
                    'constraints' => array(
                        new NotBlank(
                            array('message' => "Le nom d'utilisateur ou l'adresse email que vous avez saisi ne correspondent à aucun utilisateur.")))
                ));
    }

}