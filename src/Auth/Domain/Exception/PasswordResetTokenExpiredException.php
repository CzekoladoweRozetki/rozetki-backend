<?php

declare(strict_types=1);

namespace App\Auth\Domain\Exception;

class PasswordResetTokenExpiredException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Password reset token has expired.');
    }
}
