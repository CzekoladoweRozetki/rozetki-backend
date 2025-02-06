<?php

declare(strict_types=1);

namespace App\Test\Application\Query;

use Symfony\Component\Uid\Uuid;

readonly class TestDTO
{
    public function __construct(
        public Uuid $id,
        public string $name,
    ) {
    }

}
