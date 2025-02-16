<?php

declare(strict_types=1);

namespace App\Auth\Domain\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class PasswordResetToken
{
    use TimestampableEntity;

    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        private Uuid $token,
        #[Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $expiresAt,
        #[ManyToOne(targetEntity: User::class, fetch: 'EAGER')]
        private User $user,
    ) {
    }

    public function getToken(): Uuid
    {
        return $this->token;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt <= new \DateTimeImmutable();
    }
}
