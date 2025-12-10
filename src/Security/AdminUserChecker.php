<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User\Admin;
use App\Security\Exception\AccountDisabledException;
use App\Security\Exception\AccountLockedException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Admin) {
            return;
        }

        if (!$user->isActive()) {
            throw new AccountDisabledException('Your account is disabled. Please contact an administrator.');
        }

        if ($user->isLocked()) {
            throw new AccountLockedException(
                lockedUntil: $user->getLockedUntil(),
                message: \sprintf(
                    'Your account has been locked until %s due to too many failed login attempts.',
                    $user->getLockedUntil()?->format('Y-m-d H:i:s')
                )
            );
        }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
        if (!$user instanceof Admin) {
            return;
        }
    }
}
