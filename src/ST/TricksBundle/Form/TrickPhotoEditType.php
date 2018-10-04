<?php

namespace ST\TricksBundle\Form;

use ST\TricksBundle\Entity\TrickPhoto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickPhotoEditType extends TrickPhotoType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('photo', FileType::class,
                ['required' => false,
                    'data_class' => null,
                    'label' => false,
                    'error_bubbling' => true])
        ;
    }
}
