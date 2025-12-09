<?php

declare(strict_types=1);

namespace App\Entity\User;

use App\Entity\Trait\LifecycleCallbacksTrait;
use App\Repository\User\AdminRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ORM\Table(name: 'user_admin')]
#[ORM\UniqueConstraint(name: 'uniq_user_admin_email', fields: ['email'])]
#[ORM\Index(name: 'idx_user_admin_active', columns: ['active'])]
#[ORM\HasLifecycleCallbacks]
class Admin implements UserInterface, PasswordAuthenticatedUserInterface
{
    use LifecycleCallbacksTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    private ?string $lastName = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $active = true;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private ?int $failedLoginAttempts = 0;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastFailedLoginAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lockedUntil = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $lastLoginIp = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $resetPasswordToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resetPasswordExpiresAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getFailedLoginAttempts(): ?int
    {
        return $this->failedLoginAttempts;
    }

    public function setFailedLoginAttempts(int $failedLoginAttempts): static
    {
        $this->failedLoginAttempts = $failedLoginAttempts;

        return $this;
    }

    public function getLastFailedLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastFailedLoginAt;
    }

    public function setLastFailedLoginAt(?\DateTimeImmutable $lastFailedLoginAt): static
    {
        $this->lastFailedLoginAt = $lastFailedLoginAt;

        return $this;
    }

    public function getLockedUntil(): ?\DateTimeImmutable
    {
        return $this->lockedUntil;
    }

    public function setLockedUntil(?\DateTimeImmutable $lockedUntil): static
    {
        $this->lockedUntil = $lockedUntil;

        return $this;
    }

    public function isLocked(): bool
    {
        if (null === $this->lockedUntil) {
            return false;
        }

        return $this->lockedUntil > new \DateTimeImmutable();
    }

    public function incrementFailedLoginAttempts(int $maxAttempts = 5, int $lockDurationMinutes = 15): void
    {
        $this->failedLoginAttempts++;
        $this->lastFailedLoginAt = new \DateTimeImmutable();

        if ($this->failedLoginAttempts >= $maxAttempts) {
            $this->lockedUntil = new \DateTimeImmutable("+{$lockDurationMinutes} minutes");
        }
    }

    public function resetFailedLoginAttempts(): void
    {
        $this->failedLoginAttempts = 0;
        $this->lastFailedLoginAt   = null;
        $this->lockedUntil         = null;
    }

    public function recordSuccessfulLogin(string $ipAddress): void
    {
        $this->lastLoginAt = new \DateTimeImmutable();
        $this->lastLoginIp = $ipAddress;
        $this->resetFailedLoginAttempts();
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(\DateTimeImmutable $lastLoginAt): static
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getLastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    public function setLastLoginIp(?string $lastLoginIp): static
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): static
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    public function getResetPasswordExpiresAt(): ?\DateTimeImmutable
    {
        return $this->resetPasswordExpiresAt;
    }

    public function setResetPasswordExpiresAt(?\DateTimeImmutable $resetPasswordExpiresAt): static
    {
        $this->resetPasswordExpiresAt = $resetPasswordExpiresAt;

        return $this;
    }

    public function isResetPasswordTokenValid(): bool
    {
        if (null === $this->resetPasswordToken || null === $this->resetPasswordExpiresAt) {
            return false;
        }

        return $this->resetPasswordExpiresAt > new \DateTimeImmutable();
    }

    public function generateResetPasswordToken(int $validityHours = 2): void
    {
        $this->resetPasswordToken = bin2hex(random_bytes(32));
        $this->resetPasswordExpiresAt = new \DateTimeImmutable("+ {$validityHours} hours");
    }
}
