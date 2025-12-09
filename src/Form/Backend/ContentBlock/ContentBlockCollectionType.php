<?php

declare(strict_types=1);

namespace App\Form\Backend\ContentBlock;

use App\Entity\Content\ContentBlock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for managing a collection of content blocks.
 *
 * @extends AbstractType<ContentBlock[]>
 */
class ContentBlockCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('blocks', CollectionType::class, [
                'entry_type' => ContentBlockType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'prototype' => true,
                'prototype_name' => '__block_name__',
                'attr' => [
                    'data-controller' => 'content-block-collection',
                    'data-content-block-collection-prototype-value' => '__block_name__',
                ],
            ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['allow_add'] = true;
        $view->vars['allow_delete'] = true;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
