<?php

declare(strict_types=1);

namespace App\Entity\Content;

use App\Entity\Blog\Post;
use App\Entity\Portfolio\Project;
use App\Entity\Trait\LifecycleCallbacksTrait;
use App\Repository\Content\ContentBlockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContentBlockRepository::class)]
#[ORM\Table(name: 'content_block')]
#[ORM\Index(name: 'idx_content_block_post', columns: ['post_id'])]
#[ORM\Index(name: 'idx_content_block_project', columns: ['project_id'])]
#[ORM\HasLifecycleCallbacks]
class ContentBlock
{
    use LifecycleCallbacksTrait;

    public const string TYPE_TEXT = 'text';
    public const string TYPE_CODE = 'code';
    public const string TYPE_IMAGE = 'image';
    public const string TYPE_GALLERY = 'gallery';
    public const string TYPE_QUOTE = 'quote';
    public const string TYPE_CALLOUT = 'callout';
    public const string TYPE_EMBED = 'embed';
    public const string TYPE_DIVIDER = 'divider';
    public const string TYPE_BUTTON = 'button';
    public const string TYPE_METRIC = 'metric';
    public const string TYPE_FILE = 'file';
    public const string TYPE_INTERNAL_LINK = 'internal_link';
    public const string TYPE_RELATED_POSTS = 'related_posts';
    public const string TYPE_RELATED_PROJECTS = 'related_projects';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private string $type;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private int $position = 0;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'contentBlocks')]
    #[ORM\JoinColumn(name: 'post_id', onDelete: 'CASCADE')]
    private ?Post $post = null;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'contentBlocks')]
    #[ORM\JoinColumn(name: 'project_id', onDelete: 'CASCADE')]
    private ?Project $project = null;

    /** @var array<string, mixed> */
    #[ORM\Column(type: Types::JSON)]
    private array $data = [];

    /**
     * @var Collection<int, ContentBlockReference>
     */
    #[ORM\OneToMany(
        targetEntity: ContentBlockReference::class,
        mappedBy: 'block',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $references;

    public function __construct()
    {
        $this->references = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get a specific data field with optional default value.
     */
    public function getDataField(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Set a specific data field.
     */
    public function setDataField(string $key, mixed $value): static
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @return Collection<int, ContentBlockReference>
     */
    public function getReferences(): Collection
    {
        return $this->references;
    }

    public function addReference(ContentBlockReference $reference): static
    {
        if (!$this->references->contains($reference)) {
            $this->references->add($reference);
            $reference->setBlock($this);
        }

        return $this;
    }

    public function removeReference(ContentBlockReference $reference): static
    {
        if ($this->references->removeElement($reference)) {
            if ($reference->getBlock() === $this) {
                $reference->setBlock($this);
            }
        }

        return $this;
    }

    /**
     * Get the first media reference (for single media blocks like image).
     */
    public function getFirstMediaReference(): ?ContentBlockReference
    {
        foreach ($this->references as $ref) {
            if (null !== $ref->getMedia()) {
                return $ref;
            }
        }

        return null;
    }

    /**
     * Get all media references (for gallery blocks).
     *
     * @return ContentBlockReference[]
     */
    public function getMediaReferences(): array
    {
        return $this->references->filter(
            fn (ContentBlockReference $ref) => null !== $ref->getMedia()
        )->toArray();
    }

    /**
     * Get the first post reference (for featured post blocks).
     */
    public function getFirstPostReference(): ?ContentBlockReference
    {
        foreach ($this->references as $ref) {
            if (null !== $ref->getPost()) {
                return $ref;
            }
        }

        return null;
    }

    /**
     * Get all post references (for related posts blocks).
     *
     * @return ContentBlockReference[]
     */
    public function getPostReferences(): array
    {
        return $this->references->filter(
            fn (ContentBlockReference $ref) => null !== $ref->getPost()
        )->toArray();
    }

    /**
     * Get the first project reference (for featured project blocks).
     */
    public function getFirstProjectReference(): ?ContentBlockReference
    {
        foreach ($this->references as $ref) {
            if (null !== $ref->getProject()) {
                return $ref;
            }
        }

        return null;
    }

    /**
     * Get all project references (for related projects blocks).
     *
     * @return ContentBlockReference[]
     */
    public function getProjectReferences(): array
    {
        return $this->references->filter(
            fn (ContentBlockReference $ref) => null !== $ref->getProject()
        )->toArray();
    }
}
