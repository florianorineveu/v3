<?php

declare(strict_types=1);

namespace App\Form\Backend\Admin;

use App\Entity\User\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Admin>
 */
class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'form.user.profile.label.email',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'form.user.profile.label.first_name',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'form.user.profile.label.last_name',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'actions.save',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => Admin::class,
            'translation_domain' => 'admin',
        ]);
    }
}
