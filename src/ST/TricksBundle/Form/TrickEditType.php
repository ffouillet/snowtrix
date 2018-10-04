<?php

namespace ST\TricksBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class TrickEditType extends TrickType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('photos', CollectionType::class,
                ['label' => 'Photos de la figure : ',
                    'entry_type' => TrickPhotoEditType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'error_bubbling' => false])
            ->add('videos', CollectionType::class,
                ['entry_type' => TrickVideoEditType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Videos de la figure : ',
                    'error_bubbling' => false
                ])
            ->add('submit', SubmitType::class,
                ['label' => 'Enregistrer les modifications']);;
    }

}