<?php

declare(strict_types=1);

namespace App\Auth\Domain\Entity;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BLOCKED = 'blocked';
}
