<?php

namespace TricksBundle\Form;

use TricksBundle\Entity\Trick;
use TricksBundle\Entity\TrickGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
                    'error_bubbling' => true,
                    'attr' => ['class' => 'trick-form-description']])
            ->add('groups', EntityType::class,
                ['class' => TrickGroup::class,
                    'required' => true,
                    'label' => 'Groupes de la figure : ',
                    'choice_label' => 'name',
                    'multiple' => true,
                    'attr' => ['class' => 'select-trick-group']])
            ->add('photos', CollectionType::class,
                ['label' => 'Photos de la figure : ',
                    'entry_type' => TrickPhotoType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'error_bubbling' => false])
            ->add('videos', CollectionType::class,
                ['entry_type' => TrickVideoType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'Videos de la figure : ',
                    'error_bubbling' => false
                ])
            ->add('submit', SubmitType::class,
                ['label' => 'Ajouter la nouvelle figure']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Trick::class,
        ));
    }
}