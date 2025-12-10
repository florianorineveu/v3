<?php

declare(strict_types=1);

namespace App\EventSubscriber\Security;

use App\Entity\User\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginAttemptSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LoginFailureEvent::class => 'onLoginFailure',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof Admin) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        $ipAddress = $request?->getClientIp() ?? 'unknown';

        $user->recordSuccessfulLogin($ipAddress);

        $this->entityManager->flush();
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $passport = $event->getPassport();

        if (!$passport?->hasBadge(UserBadge::class)) {
            return;
        }

        $userBadge = $passport->getBadge(UserBadge::class);
        $user = $userBadge?->getUser();

        if (!$user instanceof Admin) {
            return;
        }

        $user->incrementFailedLoginAttempts();

        $this->entityManager->flush();

        if ($user->isLocked()) {
            throw new CustomUserMessageAuthenticationException(
                \sprintf(
                    'Your account has been locked until %s due to too many failed login attempts.',
                    $user->getLockedUntil()?->format('d/m/Y H:i:s')
                )
            );
        }
    }
}
