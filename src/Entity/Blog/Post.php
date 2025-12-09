<?php

declare(strict_types=1);

namespace App\Entity\Blog;

use App\Entity\Content\ContentBlock;
use App\Entity\Trait\LifecycleCallbacksTrait;
use App\Repository\Blog\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: 'blog_post')]
#[ORM\UniqueConstraint(name: 'uniq_blog_post_slug', fields: ['slug'])]
#[ORM\Index(name: 'idx_blog_post_enabled_slug', fields: ['enabled', 'slug'])]
#[ORM\HasLifecycleCallbacks]
class Post
{
    use LifecycleCallbacksTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $slug = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    /**
     * @var Collection<int, ContentBlock>
     */
    #[ORM\OneToMany(
        targetEntity: ContentBlock::class,
        mappedBy: 'post',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $contentBlocks;

    public function __construct()
    {
        $this->contentBlocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Collection<int, ContentBlock>
     */
    public function getContentBlocks(): Collection
    {
        return $this->contentBlocks;
    }

    public function addContentBlock(ContentBlock $contentBlock): static
    {
        if (!$this->contentBlocks->contains($contentBlock)) {
            $this->contentBlocks->add($contentBlock);
            $contentBlock->setPost($this);
        }

        return $this;
    }

    public function removeContentBlock(ContentBlock $contentBlock): static
    {
        if ($this->contentBlocks->removeElement($contentBlock)) {
            if ($contentBlock->getPost() === $this) {
                $contentBlock->setPost(null);
            }
        }

        return $this;
    }
}
