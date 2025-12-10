<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\Tool\ShortUrlRepository;

class ShortenUrl
{
    public function __construct(private ShortUrlRepository $shortUrlRepository)
    {
    }

    public function generateSlug(): string
    {
        do {
            $slug = substr(bin2hex(random_bytes(4)), 0, 9);
            $existingUrl = $this->shortUrlRepository->findOneBy(['slug' => $slug]);
        } while (null !== $existingUrl);

        return $slug;
    }
}
