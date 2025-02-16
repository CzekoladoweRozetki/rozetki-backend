<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\RequestPasswordReset;

use App\Common\Application\Command\Command;

readonly class RequestPasswordResetCommand extends Command
{
    public function __construct(
        public string $email,
    ) {
    }
}
