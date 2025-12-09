<?php

declare(strict_types=1);

namespace App\Form\Backend\ContentBlock;

use App\Entity\Blog\Post;
use App\Entity\Content\ContentBlock;
use App\Entity\Portfolio\Project;
use App\Service\ContentBlockManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Unified form type for all content block types.
 *
 * All fields are present in the form, JavaScript handles showing/hiding
 * based on the selected type.
 *
 * @extends AbstractType<ContentBlock>
 */
class ContentBlockType extends AbstractType
{
    public function __construct(
        private readonly ContentBlockManager $manager,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // === Core fields (always present) ===
            ->add('type', ChoiceType::class, [
                'choices' => array_flip($this->manager->getMvpTypes()),
                'label' => 'Type de bloc',
                'attr' => [
                    'data-content-block-item-target' => 'typeSelect',
                    'data-action' => 'content-block-item#onTypeChange',
                ],
            ])
            ->add('position', HiddenType::class)

            // === Text block fields ===
            ->add('content', TextareaType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Contenu',
                'attr' => [
                    'rows' => 10,
                    'data-block-field' => 'text',
                ],
            ])

            // === Code block fields ===
            ->add('code', TextareaType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Code',
                'attr' => [
                    'rows' => 15,
                    'class' => 'font-mono',
                    'data-block-field' => 'code',
                ],
            ])
            ->add('language', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Langage',
                'choices' => array_flip($this->manager->getCodeLanguages()),
                'attr' => [
                    'data-block-field' => 'code',
                ],
            ])
            ->add('filename', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Nom du fichier',
                'attr' => [
                    'placeholder' => 'example.php',
                    'data-block-field' => 'code',
                ],
            ])

            // === Quote block fields ===
            ->add('quoteText', TextareaType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Citation',
                'attr' => [
                    'rows' => 4,
                    'data-block-field' => 'quote',
                ],
            ])
            ->add('quoteAuthor', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Auteur',
                'attr' => [
                    'data-block-field' => 'quote',
                ],
            ])
            ->add('quoteSource', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Source',
                'attr' => [
                    'data-block-field' => 'quote',
                ],
            ])

            // === Callout block fields ===
            ->add('calloutType', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Type d\'encadré',
                'choices' => array_flip($this->manager->getCalloutTypes()),
                'attr' => [
                    'data-block-field' => 'callout',
                ],
            ])
            ->add('calloutTitle', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Titre',
                'attr' => [
                    'data-block-field' => 'callout',
                ],
            ])
            ->add('calloutContent', TextareaType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Contenu',
                'attr' => [
                    'rows' => 4,
                    'data-block-field' => 'callout',
                ],
            ])

            // === Divider block fields ===
            ->add('dividerStyle', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Style',
                'choices' => array_flip($this->manager->getDividerStyles()),
                'attr' => [
                    'data-block-field' => 'divider',
                ],
            ])

            // === Image block fields (for future use) ===
            ->add('imageCaption', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Légende',
                'attr' => [
                    'data-block-field' => 'image',
                ],
            ])
            ->add('imageAlt', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Texte alternatif',
                'attr' => [
                    'data-block-field' => 'image',
                ],
            ])
            ->add('imageAlignment', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Alignement',
                'choices' => array_flip($this->manager->getAlignments()),
                'attr' => [
                    'data-block-field' => 'image gallery',
                ],
            ])

            // === Gallery block fields (for future use) ===
            ->add('galleryLayout', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Disposition',
                'choices' => array_flip($this->manager->getGalleryLayouts()),
                'attr' => [
                    'data-block-field' => 'gallery',
                ],
            ])
            ->add('galleryColumns', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Colonnes',
                'choices' => [
                    '2 colonnes' => 2,
                    '3 colonnes' => 3,
                    '4 colonnes' => 4,
                ],
                'attr' => [
                    'data-block-field' => 'gallery related_posts related_projects',
                ],
            ])

            // === Button block fields (for future use) ===
            ->add('buttonText', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Texte du bouton',
                'attr' => [
                    'data-block-field' => 'button',
                ],
            ])
            ->add('buttonUrl', UrlType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'URL',
                'default_protocol' => 'https',
                'attr' => [
                    'data-block-field' => 'button embed',
                ],
            ])
            ->add('buttonStyle', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Style',
                'choices' => array_flip($this->manager->getButtonStyles()),
                'attr' => [
                    'data-block-field' => 'button',
                ],
            ])
            ->add('buttonAlignment', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Alignement',
                'choices' => array_flip($this->manager->getAlignments()),
                'attr' => [
                    'data-block-field' => 'button',
                ],
            ])

            // === Metric block fields (for future use) ===
            ->add('metricValue', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Valeur',
                'attr' => [
                    'data-block-field' => 'metric',
                ],
            ])
            ->add('metricLabel', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Label',
                'attr' => [
                    'data-block-field' => 'metric',
                ],
            ])
            ->add('metricPrefix', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Préfixe',
                'attr' => [
                    'data-block-field' => 'metric',
                ],
            ])
            ->add('metricSuffix', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Suffixe',
                'attr' => [
                    'data-block-field' => 'metric',
                ],
            ])

            // === Internal link fields (for future use) ===
            ->add('internalLinkDisplay', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Affichage',
                'choices' => array_flip($this->manager->getInternalLinkDisplays()),
                'attr' => [
                    'data-block-field' => 'internal_link',
                ],
            ])
            ->add('internalLinkCustomText', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Texte personnalisé',
                'attr' => [
                    'data-block-field' => 'internal_link',
                ],
            ])

            // === Related content fields (for future use) ===
            ->add('relatedTitle', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Titre de la section',
                'attr' => [
                    'data-block-field' => 'related_posts related_projects',
                ],
            ])

            // === Entity references (for future use) ===
            ->add('relatedPost', EntityType::class, [
                'class' => Post::class,
                'choice_label' => 'title',
                'mapped' => false,
                'required' => false,
                'label' => 'Article lié',
                'placeholder' => 'Sélectionner un article',
                'attr' => [
                    'data-block-field' => 'internal_link featured_post',
                ],
            ])
            ->add('relatedProject', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'name',
                'mapped' => false,
                'required' => false,
                'label' => 'Projet lié',
                'placeholder' => 'Sélectionner un projet',
                'attr' => [
                    'data-block-field' => 'internal_link featured_project',
                ],
            ])
            ->add('relatedPosts', EntityType::class, [
                'class' => Post::class,
                'choice_label' => 'title',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'label' => 'Articles',
                'attr' => [
                    'data-block-field' => 'related_posts',
                ],
            ])
            ->add('relatedProjects', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'name',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'label' => 'Projets',
                'attr' => [
                    'data-block-field' => 'related_projects',
                ],
            ])
        ;

        // Transfer data from entity to form fields
        // Use POST_SET_DATA to ensure all child forms are built
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
            $block = $event->getData();
            $form = $event->getForm();

            if (!$block instanceof ContentBlock) {
                return;
            }

            $data = $block->getData();
            $type = $block->getType();

            // Map JSON data to form fields based on type
            match ($type) {
                ContentBlock::TYPE_TEXT => $this->mapDataToForm($form, $data, [
                    'content' => 'content',
                ]),
                ContentBlock::TYPE_CODE => $this->mapDataToForm($form, $data, [
                    'code' => 'code',
                    'language' => 'language',
                    'filename' => 'filename',
                ]),
                ContentBlock::TYPE_QUOTE => $this->mapDataToForm($form, $data, [
                    'text' => 'quoteText',
                    'author' => 'quoteAuthor',
                    'source' => 'quoteSource',
                ]),
                ContentBlock::TYPE_CALLOUT => $this->mapDataToForm($form, $data, [
                    'type' => 'calloutType',
                    'title' => 'calloutTitle',
                    'content' => 'calloutContent',
                ]),
                ContentBlock::TYPE_DIVIDER => $this->mapDataToForm($form, $data, [
                    'style' => 'dividerStyle',
                ]),
                default => null,
            };
        });

        // Transfer data from form fields to entity
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
            $block = $event->getData();
            $form = $event->getForm();

            if (!$block instanceof ContentBlock) {
                return;
            }

            $type = $block->getType();

            // Map form fields to JSON data based on type
            $data = match ($type) {
                ContentBlock::TYPE_TEXT => $this->mapFormToData($form, [
                    'content' => 'content',
                ]),
                ContentBlock::TYPE_CODE => $this->mapFormToData($form, [
                    'code' => 'code',
                    'language' => 'language',
                    'filename' => 'filename',
                ]),
                ContentBlock::TYPE_QUOTE => $this->mapFormToData($form, [
                    'quoteText' => 'text',
                    'quoteAuthor' => 'author',
                    'quoteSource' => 'source',
                ]),
                ContentBlock::TYPE_CALLOUT => $this->mapFormToData($form, [
                    'calloutType' => 'type',
                    'calloutTitle' => 'title',
                    'calloutContent' => 'content',
                ]),
                ContentBlock::TYPE_DIVIDER => $this->mapFormToData($form, [
                    'dividerStyle' => 'style',
                ]),
                default => [],
            };

            $block->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContentBlock::class,
        ]);
    }

    /**
     * Map JSON data keys to form field names.
     *
     * @param \Symfony\Component\Form\FormInterface<ContentBlock> $form
     * @param array<string, mixed>                                $data
     * @param array<string, string>                               $mapping
     */
    private function mapDataToForm(\Symfony\Component\Form\FormInterface $form, array $data, array $mapping): void
    {
        foreach ($mapping as $dataKey => $formField) {
            if (isset($data[$dataKey]) && $form->has($formField)) {
                $form->get($formField)->setData($data[$dataKey]);
            }
        }
    }

    /**
     * Map form field names to JSON data keys.
     *
     * @param \Symfony\Component\Form\FormInterface<ContentBlock> $form
     * @param array<string, string>                               $mapping
     *
     * @return array<string, mixed>
     */
    private function mapFormToData(\Symfony\Component\Form\FormInterface $form, array $mapping): array
    {
        $data = [];
        foreach ($mapping as $formField => $dataKey) {
            if ($form->has($formField)) {
                $value = $form->get($formField)->getData();
                if (null !== $value && '' !== $value) {
                    $data[$dataKey] = $value;
                }
            }
        }

        return $data;
    }
}
