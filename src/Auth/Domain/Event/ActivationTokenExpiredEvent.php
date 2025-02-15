<?php

declare(strict_types=1);

namespace App\Auth\Domain\Event;

use App\Common\Domain\Event;

readonly class ActivationTokenExpiredEvent extends Event
{
    public function __construct(
        public string $userId,
        public string $activationToken,
    ) {
    }
}
