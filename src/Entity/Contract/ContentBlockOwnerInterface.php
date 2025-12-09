<?php

declare(strict_types=1);

namespace App\Entity\Contract;

/**
 * Interface for entities that can own content blocks.
 *
 * Implemented by: Blog\Post, Portfolio\Project, etc.
 */
interface ContentBlockOwnerInterface
{
    public function getId(): ?int;

    /**
     * Returns the owner type identifier used to store/retrieve content blocks.
     * Should match the CONTENT_BLOCK_OWNER_TYPE constant on the entity.
     *
     * Examples: 'blog_post', 'portfolio_project'
     */
    public function getContentBlockOwnerType(): string;
}
