<?php

declare(strict_types=1);

namespace App\Auth\Domain\Event;

use App\Common\Domain\Event;

readonly class UserCreatedEvent extends Event
{
    public function __construct(
        public string $id,
        public string $email,
        public string $token,
    ) {
    }
}
