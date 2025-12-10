<?php

declare(strict_types=1);

namespace App\Form\Backend\Tool;

use App\Entity\Tool\ShortUrl;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<ShortUrl>
 */
class ShortUrlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'tool.short_url.type.label.name',
                'required' => false,
            ])
            ->add('url', UrlType::class, [
                'label' => 'tool.short_url.type.label.url',
                'required' => true,
                'default_protocol' => 'https',
            ])
            ->add('slug', TextType::class, [
                'label' => 'tool.short_url.type.label.slug',
                'required' => false,
                'help' => 'tool.short_url.type.help.slug',
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'tool.short_url.type.label.enabled',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShortUrl::class,
            'translation_domain' => 'backend',
        ]);
    }
}
