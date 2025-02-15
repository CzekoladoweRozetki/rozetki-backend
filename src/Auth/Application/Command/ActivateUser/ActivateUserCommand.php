<?php

namespace App\Auth\Application\Command\ActivateUser;

use App\Common\Application\Command\Command;

readonly class ActivateUserCommand extends Command
{
    public function __construct(
        public string $token,
    ) {
    }
}
