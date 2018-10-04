<?php

namespace ST\TricksBundle\Form;

use ST\TricksBundle\Entity\TrickVideo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickVideoEditType extends TrickVideoType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('embedCode', TextType::class,
                ['required' => false,
                    'label' => 'Code Embed de la Video : '])
        ;
    }
}
