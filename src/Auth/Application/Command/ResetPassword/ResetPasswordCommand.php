<?php

declare(strict_types=1);

namespace App\Auth\Application\Command\ResetPassword;

use App\Common\Application\Command\Command;

readonly class ResetPasswordCommand extends Command
{
    public function __construct(public string $token, public string $newPassword)
    {
    }
}
