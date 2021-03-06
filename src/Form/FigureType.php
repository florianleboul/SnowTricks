<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Figure;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FigureType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function(CategoryRepository $categoryRepository){
                    return $categoryRepository->findAllOrderByNameQB();
                }
            ])
            ->add('videos', CollectionType::class, [
                'by_reference' => false,
                'entry_type' => VideoType::class,
                'entry_options' => ['label' => false],
                'allow_add'=>true,
                'allow_delete' => true,
                'prototype' => true
            ])
            ->add('pictures', CollectionType::class, [
                'by_reference' => false,
                'entry_type' => PictureType::class,
                'entry_options' => ['label' => false],
                'allow_add'=>true,
                'allow_delete' => true,
                'prototype' => true
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Figure::class
        ]);
    }
}
