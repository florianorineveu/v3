<?php

declare(strict_types=1);

namespace App\Entity\Blog;

use App\Entity\Trait\LifecycleCallbacksTrait;
use App\Repository\Blog\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'blog_category')]
#[ORM\UniqueConstraint(name: 'uniq_blog_category_slug', fields: ['slug'])]
#[ORM\HasLifecycleCallbacks]
class Category
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
}
