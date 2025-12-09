<?php

declare(strict_types=1);

namespace App\Entity\Content;

use App\Entity\Blog\Post;
use App\Entity\Portfolio\Project;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'content_block_reference')]
#[ORM\Index(name: 'idx_content_block_reference_block', columns: ['block_id'])]
class ContentBlockReference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ContentBlock::class, inversedBy: 'references')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ContentBlock $block;

    // Note: Media relation will be added when Media entity is created
    // #[ORM\ManyToOne(targetEntity: Media::class)]
    // #[ORM\JoinColumn(onDelete: 'CASCADE')]
    // private ?Media $media = null;

    #[ORM\Column(nullable: true)]
    private ?int $mediaId = null;

    #[ORM\ManyToOne(targetEntity: Post::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Post $post = null;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Project $project = null;

    #[ORM\Column(type: 'smallint', options: ['default' => 0])]
    private int $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlock(): ContentBlock
    {
        return $this->block;
    }

    public function setBlock(ContentBlock $block): static
    {
        $this->block = $block;

        return $this;
    }

    /**
     * Temporary: get media ID until Media entity is created.
     */
    public function getMediaId(): ?int
    {
        return $this->mediaId;
    }

    /**
     * Temporary: set media ID until Media entity is created.
     */
    public function setMediaId(?int $mediaId): static
    {
        $this->mediaId = $mediaId;

        return $this;
    }

    /**
     * Placeholder for future Media relation.
     */
    public function getMedia(): mixed
    {
        return null;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Check if this reference has any linked entity.
     */
    public function hasReference(): bool
    {
        return null !== $this->mediaId
            || null !== $this->post
            || null !== $this->project;
    }

    /**
     * Get the type of reference (media, post, project).
     */
    public function getReferenceType(): ?string
    {
        if (null !== $this->mediaId) {
            return 'media';
        }
        if (null !== $this->post) {
            return 'post';
        }
        if (null !== $this->project) {
            return 'project';
        }

        return null;
    }
}
