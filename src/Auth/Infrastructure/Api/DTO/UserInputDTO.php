<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Api\DTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UserInputDTO
{
    public function __construct(
        #[Assert\Email(message: 'The email is not a valid email.')]
        public string $email,
        #[Assert\Length(min: 8, minMessage: 'The password must be at least 8 characters long.')]
        public string $password,
    ) {
    }
}
