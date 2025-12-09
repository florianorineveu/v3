<?php

declare(strict_types=1);

namespace App\Form\Backend\Content;

use App\Entity\Blog\Category;
use App\Entity\Blog\Post;
use App\Entity\Content\ContentBlock;
use App\Form\Backend\ContentBlock\ContentBlockType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Post>
 */
class BlogPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('slug')
            ->add('publishedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('enabled')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('contentBlocks', CollectionType::class, [
                'entry_type' => ContentBlockType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'mapped' => false,
                'label' => false,
                'prototype' => true,
                'prototype_name' => '__block_name__',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
