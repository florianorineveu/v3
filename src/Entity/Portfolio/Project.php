<?php

declare(strict_types=1);

namespace App\Entity\Portfolio;

use App\Entity\Content\ContentBlock;
use App\Repository\Portfolio\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\Table(name: 'portfolio_project')]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, ContentBlock>
     */
    #[ORM\OneToMany(
        targetEntity: ContentBlock::class,
        mappedBy: 'project',
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $contentBlock->setProject($this);
        }

        return $this;
    }

    public function removeContentBlock(ContentBlock $contentBlock): static
    {
        if ($this->contentBlocks->removeElement($contentBlock)) {
            if ($contentBlock->getProject() === $this) {
                $contentBlock->setProject(null);
            }
        }

        return $this;
    }
}
