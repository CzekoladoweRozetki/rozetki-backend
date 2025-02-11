<?php

declare(strict_types=1);

namespace App\Auth\Domain\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[Table(name: 'user_account')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        #[Id]
        #[Column(type: UuidType::NAME)]
        private Uuid $id,
        #[Column(type: 'string', length: 180, unique: true)]
        private string $email,
        #[Column(type: 'string', length: 255)]
        private string $password,
    ) {
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setPassword(string $hashPassword): void
    {
        $this->password = $hashPassword;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
