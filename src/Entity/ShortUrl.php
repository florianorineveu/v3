<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\LifecycleCallbacksTrait;
use App\Entity\User\Admin;
use App\Repository\ShortUrlRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShortUrlRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_SLUG', fields: ['slug'])]
#[ORM\Index(name: 'IDX_ENABLED_SLUG', fields: ['slug', 'enabled'])]
#[ORM\HasLifecycleCallbacks]
class ShortUrl
{
    use LifecycleCallbacksTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $slug = null;

    #[ORM\ManyToOne]
    private ?Admin $createdBy = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $enabled = null;

    #[ORM\Column(options: ['default' => 0])]
    private ?int $used = null;

    public function __construct()
    {
        $this->enabled = true;
        $this->used = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

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

    public function getCreatedBy(): ?Admin
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Admin $createdBy): static
    {
        $this->createdBy = $createdBy;

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

    public function getUsed(): ?int
    {
        return $this->used;
    }

    public function setUsed(int $used): static
    {
        $this->used = $used;

        return $this;
    }
}
