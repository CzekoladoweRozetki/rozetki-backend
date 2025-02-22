<?php

declare(strict_types=1);

namespace App\Auth\Domain;

enum UserRole: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';
}
