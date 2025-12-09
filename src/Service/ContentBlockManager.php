<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Blog\Post;
use App\Entity\Content\ContentBlock;
use App\Entity\Content\ContentBlockReference;
use App\Entity\Portfolio\Project;
use App\Repository\Content\ContentBlockRepository;
use Doctrine\ORM\EntityManagerInterface;

class ContentBlockManager
{
    public function __construct(
        private readonly ContentBlockRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Load all content blocks for a Post with eager loading.
     *
     * @return ContentBlock[]
     */
    public function loadBlocksForPost(\App\Entity\Blog\Post $post): array
    {
        if (null === $post->getId()) {
            return [];
        }

        return $this->repository->findByPostWithRelations($post);
    }

    /**
     * Load all content blocks for a Project with eager loading.
     *
     * @return ContentBlock[]
     */
    public function loadBlocksForProject(\App\Entity\Portfolio\Project $project): array
    {
        if (null === $project->getId()) {
            return [];
        }

        return $this->repository->findByProjectWithRelations($project);
    }

    /**
     * Save content blocks for a Post.
     * Handles: adding new blocks, updating existing, removing deleted.
     *
     * @param ContentBlock[] $blocks
     */
    public function saveBlocksForPost(Post $post, array $blocks): void
    {
        if (null === $post->getId()) {
            throw new \InvalidArgumentException('Post must be persisted before saving blocks');
        }

        // Get existing blocks from DB
        $existingBlocks = $this->repository->findBy(['post' => $post]);

        // Index existing blocks by ID
        $existingById = [];
        foreach ($existingBlocks as $block) {
            $id = $block->getId();
            if (null !== $id) {
                $existingById[$id] = $block;
            }
        }

        // Track which IDs we've processed
        $processedIds = [];

        // Process each block from the form
        foreach ($blocks as $block) {
            $blockId = $block->getId();

            if (null !== $blockId && isset($existingById[$blockId])) {
                // Update existing block - use the entity from DB to ensure it's managed
                $existingBlock = $existingById[$blockId];

                // Cast to int in case HiddenType returns a string
                $newPosition = (int) $block->getPosition();

                // Always set position to ensure Doctrine detects the change
                $existingBlock->setPosition($newPosition);
                $existingBlock->setType($block->getType());
                $existingBlock->setData($block->getData());
                // References are already handled by cascade persist
                $processedIds[] = $blockId;
            } else {
                // New block
                $block->setPost($post);
                $block->setPosition((int) $block->getPosition());
                $this->em->persist($block);
            }
        }

        // Remove blocks that weren't in the submitted data
        foreach ($existingById as $id => $existingBlock) {
            if (!\in_array($id, $processedIds, true)) {
                $this->em->remove($existingBlock);
            }
        }
    }

    /**
     * Save content blocks for a Project.
     * Handles: adding new blocks, updating existing, removing deleted.
     *
     * @param ContentBlock[] $blocks
     */
    public function saveBlocksForProject(Project $project, array $blocks): void
    {
        if (null === $project->getId()) {
            throw new \InvalidArgumentException('Project must be persisted before saving blocks');
        }

        // Get existing blocks from DB
        $existingBlocks = $this->repository->findBy(['project' => $project]);

        // Index existing blocks by ID
        $existingById = [];
        foreach ($existingBlocks as $block) {
            $id = $block->getId();
            if (null !== $id) {
                $existingById[$id] = $block;
            }
        }

        // Track which IDs we've processed
        $processedIds = [];

        // Process each block from the form
        foreach ($blocks as $block) {
            $blockId = $block->getId();

            if (null !== $blockId && isset($existingById[$blockId])) {
                // Update existing block - use the entity from DB to ensure it's managed
                $existingBlock = $existingById[$blockId];

                // Cast to int in case HiddenType returns a string
                $newPosition = (int) $block->getPosition();

                // Always set position to ensure Doctrine detects the change
                $existingBlock->setPosition($newPosition);
                $existingBlock->setType($block->getType());
                $existingBlock->setData($block->getData());
                // References are already handled by cascade persist
                $processedIds[] = $blockId;
            } else {
                // New block
                $block->setProject($project);
                $block->setPosition((int) $block->getPosition());
                $this->em->persist($block);
            }
        }

        // Remove blocks that weren't in the submitted data
        foreach ($existingById as $id => $existingBlock) {
            if (!\in_array($id, $processedIds, true)) {
                $this->em->remove($existingBlock);
            }
        }
    }

    /**
     * Create a new content block of the specified type.
     */
    public function createBlock(string $type): ContentBlock
    {
        $block = new ContentBlock();
        $block->setType($type);

        return $block;
    }

    /**
     * Create a new reference and attach it to a block.
     */
    public function createReference(ContentBlock $block, int $position = 0): ContentBlockReference
    {
        $reference = new ContentBlockReference();
        $reference->setPosition($position);
        $block->addReference($reference);

        return $reference;
    }

    /**
     * Get available block types with labels.
     *
     * @return array<string, string>
     */
    public function getAvailableTypes(): array
    {
        return [
            ContentBlock::TYPE_TEXT => 'Texte enrichi',
            ContentBlock::TYPE_CODE => 'Code',
            ContentBlock::TYPE_IMAGE => 'Image',
            ContentBlock::TYPE_GALLERY => 'Galerie',
            ContentBlock::TYPE_QUOTE => 'Citation',
            ContentBlock::TYPE_CALLOUT => 'Encadré',
            ContentBlock::TYPE_EMBED => 'Contenu externe',
            ContentBlock::TYPE_DIVIDER => 'Séparateur',
            ContentBlock::TYPE_BUTTON => 'Bouton/CTA',
            ContentBlock::TYPE_METRIC => 'Métrique',
            ContentBlock::TYPE_FILE => 'Fichier',
            ContentBlock::TYPE_INTERNAL_LINK => 'Lien interne',
            ContentBlock::TYPE_RELATED_POSTS => 'Articles liés',
            ContentBlock::TYPE_RELATED_PROJECTS => 'Projets liés',
        ];
    }

    /**
     * Get block types that are ready for use (Phase 1 MVP).
     *
     * @return array<string, string>
     */
    public function getMvpTypes(): array
    {
        return [
            ContentBlock::TYPE_TEXT => 'Texte enrichi',
            ContentBlock::TYPE_CODE => 'Code',
            ContentBlock::TYPE_QUOTE => 'Citation',
            ContentBlock::TYPE_CALLOUT => 'Encadré',
            ContentBlock::TYPE_DIVIDER => 'Séparateur',
        ];
    }

    /**
     * Get default data for a block type.
     *
     * @return array<string, mixed>
     */
    public function getDefaultData(string $type): array
    {
        return match ($type) {
            ContentBlock::TYPE_TEXT => ['content' => ''],
            ContentBlock::TYPE_CODE => ['code' => '', 'language' => 'php', 'filename' => ''],
            ContentBlock::TYPE_QUOTE => ['text' => '', 'author' => '', 'source' => ''],
            ContentBlock::TYPE_CALLOUT => ['type' => 'info', 'title' => '', 'content' => ''],
            ContentBlock::TYPE_IMAGE => ['caption' => '', 'alt' => '', 'alignment' => 'center'],
            ContentBlock::TYPE_GALLERY => ['layout' => 'grid', 'columns' => 3],
            ContentBlock::TYPE_EMBED => ['url' => '', 'provider' => ''],
            ContentBlock::TYPE_DIVIDER => ['style' => 'line'],
            ContentBlock::TYPE_BUTTON => ['text' => '', 'url' => '', 'style' => 'primary', 'alignment' => 'left'],
            ContentBlock::TYPE_METRIC => ['value' => '', 'label' => '', 'prefix' => '', 'suffix' => ''],
            ContentBlock::TYPE_FILE => ['title' => '', 'description' => ''],
            ContentBlock::TYPE_INTERNAL_LINK => ['display' => 'card', 'custom_text' => ''],
            ContentBlock::TYPE_RELATED_POSTS => ['title' => '', 'columns' => 2],
            ContentBlock::TYPE_RELATED_PROJECTS => ['title' => '', 'columns' => 2],
            default => [],
        };
    }

    /**
     * Get supported programming languages for code blocks.
     *
     * @return array<string, string>
     */
    public function getCodeLanguages(): array
    {
        return [
            'php' => 'PHP',
            'javascript' => 'JavaScript',
            'typescript' => 'TypeScript',
            'html' => 'HTML',
            'css' => 'CSS',
            'scss' => 'SCSS',
            'bash' => 'Bash',
            'sql' => 'SQL',
            'yaml' => 'YAML',
            'json' => 'JSON',
            'twig' => 'Twig',
            'markdown' => 'Markdown',
            'plaintext' => 'Texte brut',
        ];
    }

    /**
     * Get callout types.
     *
     * @return array<string, string>
     */
    public function getCalloutTypes(): array
    {
        return [
            'info' => 'Information',
            'warning' => 'Avertissement',
            'tip' => 'Astuce',
            'danger' => 'Danger',
            'success' => 'Succès',
        ];
    }

    /**
     * Get alignment options.
     *
     * @return array<string, string>
     */
    public function getAlignments(): array
    {
        return [
            'left' => 'Gauche',
            'center' => 'Centre',
            'right' => 'Droite',
            'full' => 'Pleine largeur',
        ];
    }

    /**
     * Get divider styles.
     *
     * @return array<string, string>
     */
    public function getDividerStyles(): array
    {
        return [
            'line' => 'Ligne',
            'dots' => 'Points',
            'space' => 'Espace',
        ];
    }

    /**
     * Get button styles.
     *
     * @return array<string, string>
     */
    public function getButtonStyles(): array
    {
        return [
            'primary' => 'Principal',
            'secondary' => 'Secondaire',
            'outline' => 'Contour',
            'link' => 'Lien',
        ];
    }

    /**
     * Get gallery layouts.
     *
     * @return array<string, string>
     */
    public function getGalleryLayouts(): array
    {
        return [
            'grid' => 'Grille',
            'masonry' => 'Masonry',
            'slider' => 'Slider',
        ];
    }

    /**
     * Get internal link display options.
     *
     * @return array<string, string>
     */
    public function getInternalLinkDisplays(): array
    {
        return [
            'card' => 'Carte',
            'inline' => 'En ligne',
            'button' => 'Bouton',
        ];
    }
}
