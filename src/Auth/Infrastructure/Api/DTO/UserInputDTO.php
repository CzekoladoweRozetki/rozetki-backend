<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Api\DTO;

readonly class UserInputDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }
}
