<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\RegisterUserCommand;

use App\Common\Application\Command\Command;
use Symfony\Component\Uid\Uuid;

readonly class RegisterUserCommand extends Command
{
    public function __construct(
        public Uuid $id,
        public string $email,
        public string $password,
    ) {
    }
}
