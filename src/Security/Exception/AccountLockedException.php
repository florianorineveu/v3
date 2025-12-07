<?php

declare(strict_types=1);

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AccountLockedException extends AccountStatusException
{
    public function __construct(
        private readonly ?\DateTimeImmutable $lockedUntil = null,
        string $message = 'Your account has been locked due to too many failed login attempts.',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getLockedUntil(): ?\DateTimeImmutable
    {
        return $this->lockedUntil;
    }

    public function getMessageKey(): string
    {
        return 'Account locked due to too many failed login attempts.';
    }

    public function getMessageData(): array
    {
        return [
            'locked_until' => $this->lockedUntil?->format('Y-m-d H:i:s'),
        ];
    }
}
