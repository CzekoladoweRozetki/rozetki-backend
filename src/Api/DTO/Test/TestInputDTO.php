<?php

declare(strict_types=1);

namespace App\Api\DTO\Test;

readonly class TestInputDTO
{
    public function __construct(
        public string $name,
    ) {
    }

}
