<?php

namespace App\Auth\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class ActivationToken
{
    use TimestampableEntity;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: UuidType::NAME)]
        private Uuid $id,
        #[ORM\Column(type: 'string', length: 180, unique: true)]
        private string $token,
        #[ORM\Column(type: 'string', length: 180, unique: true)]
        private string $email,
        #[ORM\Column(type: 'datetime')]
        private \DateTimeInterface $expiresAt,
    ) {
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    public static function create(string $email): self
    {
        return new self(
            id: Uuid::v4(),
            token: bin2hex(random_bytes(32)),
            email: $email,
            expiresAt: new \DateTime('+1 day'),
        );
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
