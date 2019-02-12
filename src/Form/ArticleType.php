<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre'
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => 'Contenu'
                ]
            )
            ->add(
                'category',
                // select sur une entité Doctrine
                EntityType::class,
                [
                    'label' => 'Catégorie',
                    // nom de l'entité
                    'class' => Category::class,
                    // nom de l'attribut utilisé pour l'affichage des options
                    'choice_label' => 'name',
                    // pour avoir une 1ère option vide
                    'placeholder' => 'Choisissez une catégorie'
                ]
            )
            ->add(
                'image',
                // input type file
                FileType::class,
                [
                    'label' => 'Illustration',
                    'required' => false
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
