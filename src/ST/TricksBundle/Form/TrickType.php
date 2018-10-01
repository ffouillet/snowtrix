<?php

namespace ST\TricksBundle\Form;

use ST\TricksBundle\Entity\Trick;
use ST\TricksBundle\Entity\TrickGroup;
use ST\TricksBundle\Entity\TrickPhoto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                ['required' => true,
                    'label' => 'Nom de la figure : ',
                    'error_bubbling' => true])
            ->add('description', TextareaType::class,
                ['required' => true,
                    'label' => 'Description de la figure : ',
                    'error_bubbling' => true])
            ->add('groups', EntityType::class,
                ['class' => TrickGroup::class,
                    'label' => 'Groupes de la figure : ',
                    'choice_label' => 'name',
                    'multiple' => true])
            ->add('photos', CollectionType::class,
                ['label' => 'Photos de la figure : ',
                    'entry_type' => TrickPhotoType::class,
                    'allow_add' => true,
                    'allow_delete' => true])
            ->add('videos', CollectionType::class,
                ['entry_type' => TrickVideoType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Videos de la figure : ',
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Trick::class,
        ));
    }
}