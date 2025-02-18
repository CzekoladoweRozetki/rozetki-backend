<?php

declare(strict_types=1);

namespace App\Product\Application\Query\DTO;

use Symfony\Component\Uid\Uuid;

class ProductVariantDTO
{
    public function __construct(
        public Uuid $id,
        public string $name,
        public string $description,
        public string $slug,
    ) {
    }
}
