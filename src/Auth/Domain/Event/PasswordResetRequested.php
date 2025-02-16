<?php

declare(strict_types=1);

namespace App\Auth\Domain\Event;

use App\Common\Domain\Event;
use Symfony\Component\Uid\Uuid;

readonly class PasswordResetRequested extends Event
{
    public function __construct(
        public Uuid $userId,
        public string $email,
        public Uuid $token,
    ) {
    }
}
