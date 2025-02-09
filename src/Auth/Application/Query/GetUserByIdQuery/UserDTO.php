<?php

declare(strict_types=1);

namespace App\Auth\Application\Query\GetUserByIdQuery;

use Symfony\Component\Uid\Uuid;

readonly class UserDTO
{
    public function __construct(
        public Uuid $id,
        public string $email,
        public string $password,
    ) {
    }
}
